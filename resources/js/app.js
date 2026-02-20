import './bootstrap';
import '../css/app.css';
import '@tailwindplus/elements';
import 'flowbite';
import './filament/qr-scanner';
import Alpine from 'alpinejs'
import { registerLikesStore } from './stores/likesStore'
window.Alpine = Alpine
registerLikesStore(Alpine)
Alpine.start()