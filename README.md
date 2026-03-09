# MyCompany_CustomerBlocklist

Magento 2 module for blocking fraudulent customers during storefront checkout.

## Features

- Block checkout by customer email
- Block checkout by customer phone number
- Block checkout by customer first name and last name pair
- Whitelist override with higher priority than blacklist
- Custom frontend error message from admin configuration
- Logging of blocked checkout attempts in admin panel
- CSV export of logs
- Log cleanup actions from admin panel

## Business Rules

- The module works only on standard storefront checkout
- A customer is blocked if any one of these matches:
  - email
  - phone
  - first name + last name
- Name matching requires both first name and last name
- Whitelist entries take precedence over blacklist entries
- Rules are managed manually from the Magento admin panel

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
- blacklist rules
- whitelist rules
- custom blocking message
- blocked attempts log

## Notes

- The module is intended for storefront checkout validation
- Existing broken order payment data in the project is unrelated to the core blocking logic
- This repository contains only the Magento module source code
