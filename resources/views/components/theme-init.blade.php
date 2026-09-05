@php($defaultTheme = front_setting('default_theme', 'light'))
<script>
    (function () {
        const storedTheme = localStorage.getItem('maremiya-theme');
        const defaultTheme = @js($defaultTheme);
        let theme = storedTheme ?? defaultTheme;

        if (theme === 'system') {
            theme = window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
        }

        if (theme === 'dark') {
            document.documentElement.classList.add('dark');
        }
    })();
</script>
