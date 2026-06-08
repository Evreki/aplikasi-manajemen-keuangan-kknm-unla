<!DOCTYPE html>
<html class="dark" lang="en">
<head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title>Aplikasi Keuangan KKN | Dashboard</title>
<link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}" />
<script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
<link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&family=Inter:wght@400;500;600&display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
<script id="tailwind-config">
    tailwind.config = {
        darkMode: "class",
        theme: {
            extend: {
                "colors": {
                    "primary":                    "#38bdf8",
                    "primary-fixed":              "#38bdf8",
                    "primary-fixed-dim":          "#0ea5e9",
                    "primary-dim":                "#0ea5e9",
                    "primary-container":          "#0284c7",
                    "on-primary":                 "#082f49",
                    "on-primary-fixed":           "#0f172a",
                    "on-primary-fixed-variant":   "#0369a1",
                    "on-primary-container":       "#f0f9ff",
                    "secondary":                  "#6e9bff",
                    "secondary-fixed":            "#c0d1ff",
                    "secondary-fixed-dim":        "#acc3ff",
                    "secondary-dim":              "#0f6df3",
                    "secondary-container":        "#0058ca",
                    "on-secondary":               "#001d4e",
                    "on-secondary-fixed":         "#003076",
                    "on-secondary-fixed-variant": "#004baf",
                    "on-secondary-container":     "#f7f7ff",
                    "tertiary":                   "#cb7bff",
                    "tertiary-fixed":             "#d797ff",
                    "tertiary-fixed-dim":         "#cf83ff",
                    "tertiary-dim":               "#ae25ff",
                    "tertiary-container":         "#ab08ff",
                    "on-tertiary":                "#360055",
                    "on-tertiary-fixed":          "#2a0044",
                    "on-tertiary-fixed-variant":  "#5b008c",
                    "on-tertiary-container":      "#ffffff",
                    "surface":                    "#0e0e0e",
                    "surface-dim":                "#0e0e0e",
                    "surface-bright":             "#2c2c2c",
                    "surface-variant":            "#262626",
                    "surface-tint":               "#38bdf8",
                    "surface-container-lowest":   "#000000",
                    "surface-container-low":      "#131313",
                    "surface-container":          "#1a1a1a",
                    "surface-container-high":     "#20201f",
                    "surface-container-highest":  "#262626",
                    "on-surface":                 "#ffffff",
                    "on-surface-variant":         "#adaaaa",
                    "background":                 "#0e0e0e",
                    "on-background":              "#ffffff",
                    "outline":                    "#767575",
                    "outline-variant":            "#484847",
                    "inverse-primary":            "#0c4a6e",
                    "inverse-surface":            "#fcf9f8",
                    "inverse-on-surface":         "#565555",
                    "error":                      "#ff716c",
                    "error-dim":                  "#d7383b",
                    "error-container":            "#9f0519",
                    "on-error":                   "#490006",
                    "on-error-container":         "#ffa8a3"
                },
                "borderRadius": {
                    "DEFAULT": "0.25rem",
                    "lg":      "0.5rem",
                    "xl":      "0.75rem",
                    "full":    "9999px"
                },
                "fontFamily": {
                    "headline": ["Manrope"],
                    "body":     ["Inter"],
                    "label":    ["Inter"]
                }
            }
        }
    }
</script>
    <style>
        [x-cloak] { display: none !important; }
        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
        }
        .glass-card {
            background: rgba(38, 38, 38, 0.6);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
        }
        .no-scrollbar::-webkit-scrollbar { display: none; }
    </style>
    @filamentStyles
    @livewireStyles
</head>
<body class="bg-background text-on-surface font-body min-h-screen">
    {{ $slot }}
    @filamentScripts
    @livewireScripts
    @livewire('notifications')
</body>
</html>
