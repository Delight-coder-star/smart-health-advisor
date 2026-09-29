/**
 * Video Recorder JavaScript
 * Handles camera access, recording, and preview for eye scan
 */

document.addEventListener('DOMContentLoaded', function () {
    const preview = document.getElementById('preview');
    const recordBtn = document.getElementById('recordBtn');
    const stopBtn = document.getElementById('stopBtn');
    const retakeBtn = document.getElementById('retakeBtn');
    const countdownOverlay = document.getElementById('countdownOverlay');
    const countdownText = document.getElementById('countdownText');
    const statusMessage = document.getElementById('statusMessage');

    if (!preview || !recordBtn) return;

    let mediaRecorder = null;
    let recordedChunks = [];
    let stream = null;
    let countdownInterval = null;

    const RECORDING_DURATION = 10; // seconds

    // ===== Camera Access =====
    async function startCamera() {
        try {
            stream = await navigator.mediaDevices.getUserMedia({
                video: {
                    width: { ideal: 640 },
                    height: { ideal: 480 },
                    facingMode: 'user',
                },
                audio: false,
            });

            preview.srcObject = stream;
            preview.style.display = 'block';

            if (statusMessage) {
                statusMessage.textContent = 'Camera ready. Click Start Recording.';
                statusMessage.className = 'mt-3 text-success';
            }

            return true;
        } catch (error) {
            console.error('Camera error:', error);
            if (statusMessage) {
                statusMessage.textContent = 'Unable to access camera. Please check permissions. You can skip this step.';
                statusMessage.className = 'mt-3 text-danger';
            }
            return false;
        }
    }

    // ===== Start Recording =====
    async function startRecording() {
        if (!stream) {
            const ok = await startCamera();
            if (!ok) return;
        }

        recordedChunks = [];

        const mimeTypes = [
            'video/webm;codecs=vp9',
            'video/webm;codecs=vp8',
            'video/webm',
            'video/mp4',
        ];

        let selectedMime = '';
        for (const mime of mimeTypes) {
            if (MediaRecorder.isTypeSupported(mime)) {
                selectedMime = mime;
                break;
            }
        }

        try {
            mediaRecorder = new MediaRecorder(stream, selectedMime ? { mimeType: selectedMime } : {});

            mediaRecorder.ondataavailable = (event) => {
                if (event.data && event.data.size > 0) {
                    recordedChunks.push(event.data);
                }
            };

            mediaRecorder.onstop = () => {
                const blob = new Blob(recordedChunks, { type: 'video/webm' });
                const file = new File([blob], `eye-scan-${Date.now()}.webm`, { type: 'video/webm' });

                // Attach to hidden file input (create if needed)
                const existingInput = document.querySelector('input[name="video"]');
                if (existingInput) existingInput.remove();

                const fileInput = document.createElement('input');
                fileInput.type = 'file';
                fileInput.name = 'video';
                fileInput.style.display = 'none';

                const dataTransfer = new DataTransfer();
                dataTransfer.items.add(file);
                fileInput.files = dataTransfer.files;

                // Attach to form
                const form = document.getElementById('assessmentForm') || document.querySelector('form');
                if (form) form.appendChild(fileInput);

                // Show playback
                preview.srcObject = null;
                preview.src = URL.createObjectURL(blob);
                preview.controls = true;
                preview.muted = false;
                preview.loop = true;
                preview.play();

                if (statusMessage) {
                    statusMessage.textContent = 'Recording complete. Video attached to your assessment.';
                    statusMessage.className = 'mt-3 text-success';
                }

                if (retakeBtn) retakeBtn.disabled = false;
            };

            mediaRecorder.start();

            if (recordBtn) recordBtn.disabled = true;
            if (stopBtn) stopBtn.disabled = false;

            // Countdown
            let timeLeft = RECORDING_DURATION;
            if (countdownOverlay) countdownOverlay.classList.remove('d-none');
            if (countdownOverlay) countdownOverlay.classList.add('d-flex');
            if (countdownText) countdownText.textContent = timeLeft;

            if (statusMessage) {
                statusMessage.textContent = 'Recording... Keep your eyes on the camera.';
                statusMessage.className = 'mt-3 text-danger pulse';
            }

            countdownInterval = setInterval(() => {
                timeLeft--;
                if (countdownText) countdownText.textContent = timeLeft;

                if (timeLeft <= 0) {
                    clearInterval(countdownInterval);
                    stopRecording();
                }
            }, 1000);

        } catch (error) {
            console.error('Recording error:', error);
            if (statusMessage) {
                statusMessage.textContent = 'Recording failed: ' + error.message;
                statusMessage.className = 'mt-3 text-danger';
            }
        }
    }

    // ===== Stop Recording =====
    function stopRecording() {
        if (countdownInterval) {
            clearInterval(countdownInterval);
            countdownInterval = null;
        }

        if (countdownOverlay) {
            countdownOverlay.classList.add('d-none');
            countdownOverlay.classList.remove('d-flex');
        }

        if (mediaRecorder && mediaRecorder.state !== 'inactive') {
            mediaRecorder.stop();
        }

        if (recordBtn) recordBtn.disabled = false;
        if (stopBtn) stopBtn.disabled = true;
    }

    // ===== Retake =====
    async function retake() {
        recordedChunks = [];

        const existingInput = document.querySelector('input[name="video"]');
        if (existingInput) existingInput.remove();

        preview.controls = false;
        preview.muted = true;
        preview.loop = false;

        await startCamera();

        if (retakeBtn) retakeBtn.disabled = true;
        if (recordBtn) recordBtn.disabled = false;

        if (statusMessage) {
            statusMessage.textContent = 'Camera ready. Click Start Recording.';
            statusMessage.className = 'mt-3 text-muted';
        }
    }

    // ===== Attach Events =====
    if (recordBtn) {
        recordBtn.addEventListener('click', startRecording);
    }

    if (stopBtn) {
        stopBtn.addEventListener('click', stopRecording);
    }

    if (retakeBtn) {
        retakeBtn.addEventListener('click', retake);
    }

    // ===== Initialize camera on load =====
    startCamera();

    // ===== Cleanup on unload =====
    window.addEventListener('beforeunload', () => {
        if (stream) {
            stream.getTracks().forEach((track) => track.stop());
        }
    });
});video-recorder.js