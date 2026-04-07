function confirmDelete(nombre) {
    if (confirm('¿Estás seguro de que deseas eliminar este país?')) {
        document.getElementById('deleteId').value = nombre;
        document.getElementById('deleteForm').submit();
    }
}

document.addEventListener('DOMContentLoaded', function() {
    const themes = {
        'solar': 'css/themes/solar.min.css',
        'slate': 'css/themes/slate.min.css',
        'sketchy': 'css/themes/sketchy.min.css',
        'quartz': 'css/themes/quartz.min.css'
    };

    const lightThemes = ['solar', 'slate', 'quartz'];
    const select = document.getElementById('tema');
    const themeLink = document.getElementById('theme-link');

    const savedTheme = localStorage.getItem('selectedTheme') || 'quartz';
    if (savedTheme && themes[savedTheme]) {
        select.value = savedTheme;
        themeLink.href = themes[savedTheme];
        if (lightThemes.includes(savedTheme)) {
            document.body.classList.add('theme-light-icon');
        }
    } else {
        themeLink.href = themes['quartz'];
        select.value = 'quartz';
        document.body.classList.add('theme-light-icon');
    }

    select.addEventListener('change', function() {
        const theme = this.value;
        localStorage.setItem('selectedTheme', theme);
        themeLink.href = themes[theme];

        if (lightThemes.includes(theme)) {
            document.body.classList.add('theme-light-icon');
        } else {
            document.body.classList.remove('theme-light-icon');
        }
    });
});