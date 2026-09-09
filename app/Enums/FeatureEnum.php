<?php

namespace App\Enums;

enum FeatureEnum: string
{
    // Reporting & Dashboards
    case BASIC_REPORTS = 'basic_reports';
    case ADVANCED_REPORTS = 'advanced_reports';

    // Inventory & Products
    case INVENTORY_MANAGEMENT = 'inventory_management';
    case RECIPE_MANAGEMENT = 'recipe_management';

    // Marketing & Promotions
    case PROMO_MANAGEMENT = 'promo_management';
    case CUSTOMER_LOYALTY = 'customer_loyalty';

    // Integrations
    // case MARKETPLACE_INTEGRATION = 'marketplace_integration';

    // Account Limits
    case MULTI_OUTLET = 'multi_outlet';
    case UNLIMITED_USERS = 'unlimited_users';
}
