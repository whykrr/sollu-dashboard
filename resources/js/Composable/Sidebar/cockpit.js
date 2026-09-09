import {
    faChartPie,
    faStore,
    faTags,
    faFileInvoice,
    faUsersGear,
    faLayerGroup,
    faBuildingColumns
} from '@fortawesome/free-solid-svg-icons'

export const cockpitSidebars = [
    {
        route: 'cockpit.dashboard',
        icon: faChartPie,
        label: 'Dashboard',
        permissions: '',
        activeRoute: 'cockpit.dashboard',
    },
    {
        route: 'cockpit.merchants.index',
        icon: faStore,
        label: 'Merchant Management',
        permissions: '',
        activeRoute: 'cockpit.merchants',
    },
    {
        route: 'cockpit.invoices.index',
        icon: faFileInvoice,
        label: 'Invoice Langganan',
        permissions: '',
        activeRoute: 'cockpit.invoices',
    },
    {
        route: 'cockpit.subscription-plans.index',
        icon: faLayerGroup,
        label: 'Paket Langganan',
        permissions: '',
        activeRoute: 'cockpit.subscription-plans',
    },
    {
        route: 'cockpit.payment-methods.index',
        icon: faBuildingColumns,
        label: 'Rekening Manual',
        permissions: '',
        activeRoute: 'cockpit.payment-methods',
    },
    {
        route: 'cockpit.uoms.index',
        icon: faTags,
        label: 'Global UOM',
        permissions: '',
        activeRoute: 'cockpit.uoms',
    },
    {
        route: 'cockpit.config.index',
        icon: faUsersGear,
        label: 'Platform Config',
        permissions: '',
        activeRoute: 'cockpit.config',
    }
]
