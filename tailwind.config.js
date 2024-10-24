/** @type {import('tailwindcss').Config} */
import plugin from 'tailwindcss/plugin';

export default {
    content: [
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.vue',
    ],
    theme: {
        extend: {
            colors: {
                dark: '#222222',
                light: '#F0F0F0',
                main: {
                    DEFAULT: '#004AAD',   // Primary deep blue
                    light: '#3372D1',     // Lighter shade
                    lighter: '#6697E4',   // Even lighter shade
                    dark: '#003080',      // Darker shade
                    darker: '#002060',    // Even darker shade
                },
                secondary: {
                    DEFAULT: '#5DE0E6',   // Primary light blue
                    light: '#8AEDEF',     // Lighter shade
                    lighter: '#B6F4F8',   // Even lighter shade
                    dark: '#36AEB2',      // Darker shade
                    darker: '#22888D',    // Even darker shade
                },
                clay: {
                    DEFAULT: '#AD5B00',   // Warm complementary orange
                    light: '#C97C33',     // Lighter orange
                    dark: '#8B3E00',      // Darker orange
                    muted: '#F5A623',     // Muted orange
                },
                teal: {
                    DEFAULT: '#0072AD',   // Analogous teal
                    light: '#009ED1',     // Lighter teal
                    dark: '#005B80',      // Darker teal
                    muted: '#4FD6DB',     // Muted teal
                },
                turquoise: {
                    DEFAULT: '#36C7C9',   // Analogous turquoise
                    light: '#5EDFE1',     // Lighter turquoise
                    dark: '#28A0A2',      // Darker turquoise
                    muted: '#8BE3E4',     // Muted turquoise
                },
                indigo: {
                    DEFAULT: '#002AAD',   // Dark indigo
                    light: '#4C61D1',     // Lighter indigo
                    dark: '#001B80',      // Darker indigo
                    muted: '#A7B2E0',     // Muted indigo
                },
                neutral: {
                    light: '#F0F0F0',     // Light gray
                    lighter: '#F9F9F9',   // Even lighter gray
                    DEFAULT: '#E0E0E0',   // Mid-tone gray
                    dark: '#3A3A3A',      // Dark gray
                    darker: '#1F1F1F',    // Even darker gray
                    muted: '#B0B0B0',     // Muted gray
                },
                danger: {
                    DEFAULT: '#E63946',   // Red for danger alerts
                    light: '#F28B90',     // Lighter shade of red
                    dark: '#A52733',      // Darker shade of red
                    muted: '#FF6F61',     // Muted red
                },
                warning: {
                    DEFAULT: '#FFB703',   // Yellow for warnings
                    light: '#FFCF66',     // Lighter shade of yellow
                    dark: '#CC9302',      // Darker shade of yellow
                    muted: '#FFD54F',     // Muted yellow
                },
                success: {
                    DEFAULT: '#2A9D8F',   // Green for success messages
                    light: '#63D1BB',     // Lighter green
                    dark: '#1D6F64',      // Darker green
                    muted: '#B8E0D7',     // Muted green
                },
                info: {
                    DEFAULT: '#219EBC',   // Blue for informational messages
                    light: '#6FD2E3',     // Lighter blue
                    dark: '#157182',      // Darker blue
                    muted: '#B0E0E9',     // Muted blue
                },
                purple: {
                    DEFAULT: '#6A0DAD',   // Bright purple
                    light: '#A45BB5',     // Lighter purple
                    dark: '#4B0082',      // Darker purple
                    muted: '#D6A7D9',     // Muted purple
                },
                pink: {
                    DEFAULT: '#FF6F91',   // Bright pink
                    light: '#FF9EB1',     // Lighter pink
                    dark: '#C04565',      // Darker pink
                    muted: '#F1A7C5',     // Muted pink
                },
                brown: {
                    DEFAULT: '#7B4B3A',   // Brown
                    light: '#A76D5B',     // Lighter brown
                    dark: '#4A2C28',      // Darker brown
                    muted: '#C0B2A3',     // Muted brown
                },
                gray: {
                    DEFAULT: '#B0B0B0',   // Gray
                    light: '#D0D0D0',     // Lighter gray
                    dark: '#7D7D7D',      // Darker gray
                },
            },
            fontFamily: {
                'dosis': ['"Dosis"', ...'sans'],
                'dosis-bold': ['"Dosis Bold"', ...'sans']
            },
            borderWidth: {
                '1': '1px'
            },
            keyframes: {
                fadeIn: {
                    '0%': { opacity: '0' },
                    '100%': { opacity: '1' },
                },
                fadeOut: {
                    '0%': { opacity: '1' },
                    '100%': { opacity: '0' },
                },
            },
            animation: {
                fadeIn: 'fadeIn 0.5s ease-in-out',
                fadeOut: 'fadeOut 0.5s ease-in-out',
            },
        },
    },
    plugins: [
        require('@tailwindcss/forms'),
        plugin(function ({ addComponents, theme }) {
            const colors = theme('colors');
            const colorClasses = {};

            for (const [colorName, shades] of Object.entries(colors)) {
                // Only create classes for colors that have shades (not 'inherit', 'current', etc.)
                if (typeof shades === 'object') {
                    for (const [shade, colorValue] of Object.entries(shades)) {
                        // BUTTON COLORS

                        // LABELS COLORS
                        let classLabel = `.label-${colorName}`;
                        let classDotedLabel = `.doted-label-${colorName}`;
                        if (shade !== 'DEFAULT') {
                            classLabel += `-${shade}`;
                            classDotedLabel += `-${shade}`;
                        }

                        colorClasses[classLabel] = {
                            'color': colorValue,
                            'background-color': hexToRgba(colorValue, 0.1),
                        };
                        colorClasses[classDotedLabel] = {
                            'color': colorValue,
                        };
                        colorClasses[classDotedLabel + ' span'] = {
                            'background-color': colorValue,
                        };
                    }
                } else {

                    // LABELS COLORS
                    const classLabel = `.doted-label-${colorName}`;
                    colorClasses[classLabel] = {
                        'color': shades,
                    };
                    colorClasses[classLabel + ' span'] = {
                        'background-color': shades,
                    };
                }
            }
            addComponents(colorClasses);
        }),
    ],
}

// Helper function to convert hex to rgba
function hexToRgba(hex, alpha) {
    let r = parseInt(hex.slice(1, 3), 16);
    let g = parseInt(hex.slice(3, 5), 16);
    let b = parseInt(hex.slice(5, 7), 16);
    return `rgba(${r}, ${g}, ${b}, ${alpha})`;
}
