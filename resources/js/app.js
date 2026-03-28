import './bootstrap';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.store('theme', {
    dark: document.documentElement.classList.contains('dark'),
    toggle() {
        this.dark = !this.dark;
        document.documentElement.classList.toggle('dark', this.dark);
        localStorage.theme = this.dark ? 'dark' : 'light';
    }
});

Alpine.start();
