# MyCompany_CustomerBlocklist

Magento 2 module for blocking fraudulent customers during storefront checkout.

## Features

- Block checkout by customer email
- Block checkout by customer phone number
- Block checkout by customer first name and last name pair
- Single blacklist rules list with per-rule `Active` flag
- Custom frontend error message from admin configuration
- Logging of blocked checkout attempts in admin panel
- CSV export of logs
- Log cleanup actions from admin panel
- Add customer data to blacklist directly from the admin order view

## Business Rules

- The module works only on standard storefront checkout
- A customer is blocked if any one of these matches:
  - email
  - phone
  - first name + last name
- Name matching requires both first name and last name
- Rules are managed manually from the Magento admin panel
- Only active blacklist rules are applied during checkout validation
- Inactive rules remain stored in configuration but are ignored by the matcher

## Module Name

`MyCompany_CustomerBlocklist`

## Installation

Copy the module to:

```bash
app/code/MyCompany/CustomerBlocklist
```

Then run:

```bash
php bin/magento module:enable MyCompany_CustomerBlocklist
php bin/magento setup:upgrade
php bin/magento cache:flush
```

If the store is in production mode, also run:

```bash
php bin/magento setup:di:compile
```

## Admin Configuration

After installation, configure the module in Magento admin.

Available areas include:

- module enable/disable
- blacklist rules with `Active`, `Email`, `Tel`, `First`, `Last`, and `Note` columns
- custom blocking message
- blocked attempts log
- add current order customer data to blacklist from the order view page

## Blacklist Rules

- Rules are managed in a single `Blacklist Rules` table
- Each rule can match by:
  - `Email`
  - `Tel`
  - `First` + `Last`
- The `Active` checkbox disables a rule without deleting it
- Rules added from the order view are saved as active by default
- Completely empty rows are ignored on save

## Logging

- Blocked checkout attempts are stored in `mycompany_customerblocklist_attempt_log`
- Log entries contain matched field and matched value details
- Logs can be reviewed, exported to CSV, and cleared from the admin panel

## Notes

- The module is intended for storefront checkout validation
- This repository contains only the Magento module source code
