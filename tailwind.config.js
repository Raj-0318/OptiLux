/** @type {import('tailwindcss').Config} */
module.exports = {
    content: [
        "./**/*.php",
        "./**/*.html"
    ],
    theme: {
        extend: {
            colors: {
                primary: '#0F172A',
                accent: '#F59E0B',
                surface: '#F8FAFC',
            }
        }
    },
    plugins: [],
}
