<!DOCTYPE html>
<html class="dark" lang="en">
<head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title>Aplikasi Keuangan KKN | Sign In</title>
<link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}" />
<script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
<link href="https://fonts.googleapis.com/css2?family=Manrope:wght@200;400;600;800&amp;family=Inter:wght@300;400;500;600&amp;display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet"/>
<script id="tailwind-config">
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    "colors": {
                        "primary-fixed": "#38bdf8",
                        "outline-variant": "#484847",
                        "tertiary-fixed": "#d797ff",
                        "tertiary": "#cb7bff",
                        "on-primary": "#082f49",
                        "primary-fixed-dim": "#0ea5e9",
                        "surface-container-high": "#20201f",
                        "on-tertiary-fixed": "#2a0044",
                        "outline": "#767575",
                        "secondary-fixed": "#c0d1ff",
                        "on-tertiary": "#360055",
                        "surface-container-low": "#131313",
                        "on-primary-fixed": "#0f172a",
                        "secondary-fixed-dim": "#acc3ff",
                        "secondary": "#6e9bff",
                        "surface-tint": "#38bdf8",
                        "inverse-primary": "#0c4a6e",
                        "inverse-surface": "#fcf9f8",
                        "primary-container": "#0284c7",
                        "primary": "#38bdf8",
                        "tertiary-dim": "#ae25ff",
                        "secondary-dim": "#0f6df3",
                        "tertiary-fixed-dim": "#cf83ff",
                        "secondary-container": "#0058ca",
                        "on-tertiary-fixed-variant": "#5b008c",
                        "surface": "#0e0e0e",
                        "primary-dim": "#0ea5e9",
                        "on-secondary-fixed": "#003076",
                        "surface-dim": "#0e0e0e",
                        "error-dim": "#d7383b",
                        "on-background": "#ffffff",
                        "on-secondary-fixed-variant": "#004baf",
                        "inverse-on-surface": "#565555",
                        "surface-container-highest": "#262626",
                        "on-primary-fixed-variant": "#0369a1",
                        "error": "#ff716c",
                        "on-secondary": "#001d4e",
                        "surface-container": "#1a1a1a",
                        "background": "#0e0e0e",
                        "surface-container-lowest": "#000000",
                        "surface-bright": "#2c2c2c",
                        "on-secondary-container": "#f7f7ff",
                        "on-primary-container": "#f0f9ff",
                        "on-surface": "#ffffff",
                        "surface-variant": "#262626",
                        "on-surface-variant": "#adaaaa",
                        "on-error-container": "#ffa8a3",
                        "tertiary-container": "#ab08ff",
                        "on-tertiary-container": "#ffffff",
                        "on-error": "#490006",
                        "error-container": "#9f0519"
                    },
                    "borderRadius": {
                        "DEFAULT": "0.25rem",
                        "lg": "0.5rem",
                        "xl": "0.75rem",
                        "full": "9999px"
                    },
                    "fontFamily": {
                        "headline": ["Manrope"],
                        "body": ["Inter"],
                        "label": ["Inter"]
                    }
                }
            }
        }
    </script>
<style>
        body {
            background-color: #09090b; /* Zinc dark */
            color: #ffffff;
            font-family: 'Inter', sans-serif;
            overflow-x: hidden;
            margin: 0;
            padding: 0;
        }

        /* Clean Minimal Background */
        .bg-minimal-dark {
            position: relative;
            background-color: #09090b;
            background-image: radial-gradient(ellipse at top, #1e293b 0%, transparent 60%);
        }

        /* Subtle dot grid */
        .bg-minimal-dark::before {
            content: '';
            position: absolute;
            inset: 0;
            background-image: radial-gradient(rgba(255, 255, 255, 0.05) 1px, transparent 1px);
            background-size: 24px 24px;
            z-index: -1;
            pointer-events: none;
        }

        /* Distinct Solid Glass Card */
        .glass-card {
            background: rgba(24, 24, 27, 0.85); /* Distinct dark tint, not completely transparent */
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.6);
        }

        .neon-glow {
            /* Removing neon effect from the card itself to make it cleaner */
            box-shadow: 0 10px 30px -5px rgba(0, 0, 0, 0.8), inset 0 1px 0 rgba(255, 255, 255, 0.05);
        }

        .btn-primary-glow:hover {
            box-shadow: 0 0 20px rgba(56, 189, 248, 0.3);
            transform: translateY(-1px);
        }

        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
        }
    </style>
    @livewireStyles
</head>
<body class="bg-minimal-dark min-h-screen flex flex-col">

    {{ $slot }}

    @livewireScripts
</body>
</html>
