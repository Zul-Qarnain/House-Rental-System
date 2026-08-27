<!DOCTYPE html>
<html class="h-full bg-background" lang="en">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>PropTech OS - Rental Property Management</title>
    <link href="https://fonts.googleapis.com" rel="preconnect"/>
    <link crossorigin="" href="https://fonts.gstatic.com" rel="preconnect"/>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;900&amp;display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet"/>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <script id="tailwind-config">
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    "colors": {
                        "error": "#ba1a1a",
                        "on-background": "#191c1e",
                        "surface-container": "#eceef0",
                        "surface-container-highest": "#e0e3e5",
                        "on-primary-fixed": "#131b2e",
                        "surface-tint": "#565e74",
                        "inverse-on-surface": "#eff1f3",
                        "on-secondary-fixed-variant": "#38485d",
                        "on-tertiary-fixed-variant": "#005236",
                        "error-container": "#ffdad6",
                        "primary-container": "#131b2e",
                        "tertiary-fixed-dim": "#4edea3",
                        "secondary-fixed": "#d3e4fe",
                        "secondary-fixed-dim": "#b7c8e1",
                        "on-primary-fixed-variant": "#3f465c",
                        "inverse-surface": "#2d3133",
                        "tertiary": "#000000",
                        "primary-fixed": "#dae2fd",
                        "surface-bright": "#f7f9fb",
                        "on-tertiary-container": "#009668",
                        "on-error": "#ffffff",
                        "outline-variant": "#c6c6cd",
                        "on-primary": "#ffffff",
                        "background": "#f7f9fb",
                        "secondary": "#505f76",
                        "secondary-container": "#d0e1fb",
                        "surface-dim": "#d8dadc",
                        "primary": "#000000",
                        "surface-container-low": "#f2f4f6",
                        "on-surface": "#191c1e",
                        "on-primary-container": "#7c839b",
                        "surface-container-high": "#e6e8ea",
                        "inverse-primary": "#bec6e0",
                        "outline": "#76777d",
                        "surface": "#f7f9fb",
                        "on-secondary-fixed": "#0b1c30",
                        "tertiary-fixed": "#6ffbbe",
                        "on-secondary-container": "#54647a",
                        "on-tertiary-fixed": "#002113",
                        "on-surface-variant": "#45464d",
                        "on-tertiary": "#ffffff",
                        "surface-variant": "#e0e3e5",
                        "primary-fixed-dim": "#bec6e0",
                        "on-error-container": "#93000a",
                        "surface-container-lowest": "#ffffff",
                        "tertiary-container": "#002113",
                        "on-secondary": "#ffffff"
                    },
                    "borderRadius": {
                        "DEFAULT": "0.25rem",
                        "lg": "0.5rem",
                        "xl": "0.75rem",
                        "full": "9999px"
                    },
                    "spacing": {
                        "margin-mobile": "16px",
                        "container-max": "1440px",
                        "gutter": "24px",
                        "margin-desktop": "32px",
                        "base": "4px"
                    },
                    "fontFamily": {
                        "body-sm": ["Inter"],
                        "body-md": ["Inter"],
                        "label-caps": ["Inter"],
                        "headline-lg": ["Inter"],
                        "data-mono": ["Inter"],
                        "headline-xl": ["Inter"],
                        "headline-lg-mobile": ["Inter"],
                        "title-md": ["Inter"]
                    }
                }
            }
        }
    </script>
    <style>
        input:focus, select:focus, textarea:focus {
            box-shadow: 0 0 0 2px #009668 inset !important;
            border-color: transparent !important;
        }
    </style>
</head>
<body class="bg-background text-on-background font-body-md min-h-screen flex flex-col antialiased">
