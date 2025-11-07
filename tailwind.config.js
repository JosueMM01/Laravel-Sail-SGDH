const defaultTheme = require('tailwindcss/defaultTheme')

/** @type {import('tailwindcss').Config} */
module.exports = {
    darkMode: 'class',

    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.js',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Inter', ...defaultTheme.fontFamily.sans],
            },

            colors: {
                dark: {
                    'eval-0': '#151823',
                    'eval-1': '#222738',
                    'eval-2': '#2A2F42',
                    'eval-3': '#2C3142',
                },
                // Paleta de proyecto (SGDH)
                brand: '#009900',
                'brand-dark': '#006600',
                'accent-blue': '#0033cc',
                success: '#00cc00',
                warning: '#f59e0b',
                error: '#e11d48',
                neutrals: {
                    white: '#ffffff',
                    'slate-700': '#334155',
                    bg: '#f8fafc',
                },
            },
        },
    },

    plugins: [require('@tailwindcss/forms')],
}
