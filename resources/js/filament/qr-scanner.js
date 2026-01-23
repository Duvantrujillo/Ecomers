window.__QR_SCANNER_LOADED__ = true;
console.log("QR scanner loaded ✅");

import { Html5Qrcode } from "html5-qrcode";

let html5Qr = null;
let isOpen = false;
let lastCode = null;
let lastTime = 0;

function openModal() {
    const modal = document.getElementById("qrScannerModal");
    if (!modal) return;

    modal.classList.remove("hidden");
    isOpen = true;

    if (!html5Qr) {
        html5Qr = new Html5Qrcode("qr-reader");
    }

    html5Qr.start(
        { facingMode: "environment" },
        { fps: 10, qrbox: 250 },
        (decodedText) => {
            const now = Date.now();
            if (decodedText === lastCode && now - lastTime < 1500) return;

            lastCode = decodedText;
            lastTime = now;

            window.Livewire?.dispatch("qr-scanned", { code: decodedText });

            closeModal();
        }
    );
}

function closeModal() {
    const modal = document.getElementById("qrScannerModal");
    if (!modal) return;

    html5Qr?.stop().catch(() => {});
    modal.classList.add("hidden");
    isOpen = false;
}

window.addEventListener("open-qr-scanner", openModal);
window.addEventListener("close-qr-scanner", closeModal);
