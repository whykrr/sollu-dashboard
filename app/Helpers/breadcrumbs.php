<?php


if (!function_exists('generateBreadcrumbs')) {
    function generateBreadcrumbs($routeName)
    {
        $breadcrumbs = [];

        switch ($routeName) {
            case 'dashboard':
                $breadcrumbs[] = ['label' => 'Dashboard', 'url' => route($routeName)];
                break;

            case 'colors':
                $breadcrumbs[] = ['label' => 'Colors', 'url' => route($routeName)];
                break;

            case 'typography':
                $breadcrumbs[] = ['label' => 'Typography', 'url' => route($routeName)];
                break;

            case str_contains($routeName, 'base'):
                $breadcrumbs[] = ['label' => 'Base', 'url' => '#'];

                $breadcrumbs[] = match (str_replace('base.', '', $routeName)) {
                    'cards' => ['label' => 'Cards', 'url' => route($routeName)],
                    'navigation' => ['label' => 'Navigation', 'url' => route($routeName)],
                    'placeholders' => ['label' => 'Placeholders', 'url' => route($routeName)],
                    'spinners' => ['label' => 'Spinners', 'url' => route($routeName)],
                    'tables' => ['label' => 'Tables', 'url' => route($routeName)],
                    'pagination' => ['label' => 'Pagination', 'url' => route($routeName)],
                };
                break;

            case str_contains($routeName, 'buttons'):
                $breadcrumbs[] = ['label' => 'Buttons', 'url' => '#'];

                $breadcrumbs[] = match (str_replace('buttons.', '', $routeName)) {
                    'buttons' => ['label' => 'Buttons', 'url' => route($routeName)],
                    'button-group' => ['label' => 'Button Group', 'url' => route($routeName)],
                };
                break;

            case 'charts':
                $breadcrumbs[] = ['label' => 'Charts', 'url' => route($routeName)];
                break;


            case str_contains($routeName, 'forms'):
                $breadcrumbs[] = ['label' => 'Forms', 'url' => '#'];

                $breadcrumbs[] = match (str_replace('forms.', '', $routeName)) {
                    'form-control' => ['label' => 'Form Control', 'url' => route($routeName)],
                    'select' => ['label' => 'Select', 'url' => route($routeName)],
                    'radio' => ['label' => 'Radio', 'url' => route($routeName)],
                    'check' => ['label' => 'Check', 'url' => route($routeName)],
                    'input-group' => ['label' => 'Input Group', 'url' => route($routeName)],
                    'floating-label' => ['label' => 'Floating Label', 'url' => route($routeName)],
                    'layout' => ['label' => 'Layout', 'url' => route($routeName)],
                    'validation' => ['label' => 'Validation', 'url' => route($routeName)],
                };
                break;

            case str_contains($routeName, 'notification'):
                $breadcrumbs[] = ['label' => 'Notifications', 'url' => '#'];

                $breadcrumbs[] = match (str_replace('notification.', '', $routeName)) {
                    'alert' => ['label' => 'Alert', 'url' => route($routeName)],
                    'modal' => ['label' => 'Modal', 'url' => route($routeName)],
                    'badge' => ['label' => 'Badge', 'url' => route($routeName)],
                    'toast' => ['label' => 'Toast', 'url' => route($routeName)],
                };
                break;

            default:
                $breadcrumbs[] = ['label' => 'Dashboard', 'url' => route('dashboard')];
                break;
        }

        return $breadcrumbs;
    }
}
