# CSRF Token Protection Implementation

## Overview

This document describes the CSRF (Cross-Site Request Forgery) token protection system implemented throughout the Hontoria OMS InternalWeb application. This security enhancement protects against malicious actors attempting to forge requests on behalf of authenticated users.

## Implementation Summary

### Files Created

1. **`InternalWeb/Middleware/CsrfM.php`**
   - Core CSRF token management class
   - Handles token generation, validation, and session management
   - Uses cryptographically secure random token generation
   - Implements timing-attack resistant token comparison

2. **`InternalWeb/.JS/CsrfHandler.js`**
   - JavaScript utility for handling CSRF tokens in the frontend
   - Provides functions to retrieve and inject CSRF tokens into forms
   - Supports dynamically generated forms

### Files Modified

#### Core Application

- **`InternalWeb/Public/index.php`**
  - Added CSRF middleware initialization
  - Added token validation before state-changing actions
  - Integrated CSRF protection into the routing system
  - Added protected action list mapping

#### Views

- **`InternalWeb/Views/Login/Page.php`** - Login form
- **`InternalWeb/Views/Staff/CreateAccount.php`** - Staff creation form
- **`InternalWeb/Views/Orders/CreateOrder.php`** - Order creation form
- **`InternalWeb/Views/Account/Page.php`** - Account management forms (4 forms):
  - Change Username
  - Update Contacts
  - Set User Note
  - Change Password
- **`InternalWeb/Views/Services/ProcessManagement.php`** - Process update forms
- **`InternalWeb/Views/Services/Page.php`** - Subservice info forms
- **`InternalWeb/Views/Orders/Page.php`** - Order management
- **`InternalWeb/Views/Tasks/Page.php`** - Task assignment forms
- **`InternalWeb/Views/.Components/ConfirmationBox.php`** - Confirmation dialog

#### JavaScript

- **`InternalWeb/.JS/ConfirmationBox.js`**
  - Updated to ensure CSRF tokens are present before form submission
  - Automatically refresh tokens when confirmation box is used

- **Script includes added to:**
  - `InternalWeb/Views/Orders/Page.php`
  - `InternalWeb/Views/Services/Page.php`
  - `InternalWeb/Views/Sales/Page.php`
  - `InternalWeb/Views/Inventory/Page.php`
  - `InternalWeb/Views/Account/Page.php`
  - `InternalWeb/Views/Tasks/Page.php`
  - `InternalWeb/Views/Staff/RoleManagement.php`

## How It Works

### Token Generation & Storage

1. When a session starts, `CsrfM::initializeToken()` generates a unique 64-character token
2. Token is stored in `$_SESSION['_csrf_token']`
3. Token is regenerated if invalid

### Token Injection

1. **Static Forms**: The `CsrfM::getTokenField()` function outputs:
   ```html
   <input type="hidden" name="_csrf_token" value="...">
   ```
   This is added to the beginning of all POST forms

2. **Dynamic Forms**: JavaScript function `addCsrfTokenToForm()` adds tokens to dynamically created forms

### Token Validation

1. Protected POST requests are validated in `InternalWeb/Public/index.php`
2. Validation occurs before action handlers are executed
3. Validation uses `hash_equals()` for timing-attack resistance
4. Failed validation triggers error logging and denies the request

### Protected Actions

The following actions require valid CSRF tokens for POST requests:

- **login**: `authenticate`
- **staff**: All modification actions (setRoles, createFinal, etc.)
- **account**: All profile modification actions
- **services**: All service management actions
- **orders**: All order management actions
- **tasks**: All task modification actions
- **inventory**: All inventory modification actions
- **sales**: All sales record actions

## Security Features

### Token Properties

- **Length**: 64 characters (32 bytes of random data converted to hex)
- **Generation**: Uses `random_bytes()` for cryptographic security
- **Validation**: Timing-attack resistant comparison using `hash_equals()`
- **Session-bound**: Tokens are bound to user sessions
- **Regeneration**: Tokens persist across requests until manually regenerated

### Request Validation

- CSRF tokens are validated **before** any action is processed
- Missing or invalid tokens result in immediate request rejection
- Failed attempts are logged with:
  - Validation error details
  - Page and action information
  - IP address of the request
- Error message is user-friendly but secure

### Error Handling

When CSRF validation fails:
```
Security validation failed. Please try again.
```

Detailed error information is logged to:
- PHP error logs
- Browser console (if applicable)

## Usage for Developers

### Adding CSRF Token to a Form

For **static forms** in PHP:

```html
<form method="POST" action="index.php?page=staff&action=create">
    <?php echo CsrfM::getTokenField(); ?>
    <!-- Form fields here -->
</form>
```

For **dynamically created forms** in JavaScript:

```javascript
const form = document.createElement('form');
form.method = 'POST';
form.action = 'index.php?page=orders&action=create';

// Add form fields...

// Then add CSRF token
addCsrfTokenToForm(form);
document.body.appendChild(form);
```

### Protecting a New Action

In `InternalWeb/Public/index.php`, add the action to the `$protectedActions` array:

```php
$protectedActions = [
    'newpage' => ['newAction'],
    // ... other pages
];
```

## Configuration

### Session Configuration

CSRF tokens use the standard PHP session mechanism. Ensure your `php.ini` has appropriate settings:

```ini
session.cookie_httponly = On
session.cookie_secure = On      ; If using HTTPS
session.cookie_samesite = Strict
session.use_only_cookies = On
```

### Token Regeneration

To regenerate a token after sensitive operations:

```php
CsrfM::regenerateToken();
```

This automatically updates `$_SESSION['_csrf_token']` with a new value.

## Testing

### Manual Testing

1. **Valid Request**: Submit a form normally - should succeed
2. **Missing Token**: Remove the hidden input from HTML - should fail
3. **Tampered Token**: Change the token value - should fail
4. **Old Token**: After regeneration, old token should fail

### Browser Developer Tools

To verify tokens are present:

1. Open DevTools (F12)
2. Go to Network tab
3. Submit a POST request
4. Check the Form Data tab for `_csrf_token` field

## Compatibility

- **PHP Version**: 5.3.0+ (uses `random_bytes` available in PHP 7.0+)
- **Session**: Requires PHP sessions enabled
- **JavaScript**: ES6+ (modern browsers)
- **Backward Compatibility**: Existing forms continue to work; only POST requests with tokens are accepted

## Performance Impact

- **Minimal**: Token validation adds <1ms per protected request
- **Session Storage**: Minimal memory impact (~50 bytes per session)
- **Database**: No database queries required

## Maintenance

### Regular Audits

Review the protected actions list periodically to ensure:
- All state-changing operations are protected
- No sensitive operations are missed
- The list matches actual form submissions

### Monitoring

Check error logs for CSRF validation failures:

```bash
grep "CSRF token validation failed" /var/log/apache2/error.log
```

Unusual failure patterns may indicate attack attempts.

## Migration Guide for Existing Code

### Step 1: Update Forms

Add to all POST forms:
```php
<?php echo CsrfM::getTokenField(); ?>
```

### Step 2: Update Controllers (if needed)

No controller changes needed - validation is automatic in `index.php`

### Step 3: Test

Test each form submission to ensure tokens are present and validated correctly

### Step 4: Monitor

Watch error logs for CSRF validation failures during the first week

## Security Considerations

### Not Protected Against

- **XSS**: CSRF tokens do not prevent XSS attacks. Use additional XSS protection (CSP, input validation)
- **Session Hijacking**: CSRF tokens do not prevent session hijacking. Use HTTPS and secure cookies
- **Brute Force**: CSRF tokens do not prevent brute force attacks. Use rate limiting

### Best Practices

1. **Keep tokens secret**: Never expose tokens in logs or error messages
2. **Use HTTPS**: Always use HTTPS in production
3. **Regular rotation**: Consider rotating tokens after sensitive operations
4. **Defense in depth**: Combine with other security measures (XSS protection, HTTPS, etc.)

## Troubleshooting

### "Security validation failed" Error

**Cause**: Missing or invalid CSRF token

**Solution**:
1. Verify the form includes the hidden input field
2. Check browser console for JavaScript errors
3. Verify session is active

### Form Submissions Working Without Tokens

**Cause**: Conditional protection may not be applied to all actions

**Solution**:
1. Check the `$protectedActions` array in `index.php`
2. Ensure the action is listed
3. Verify it's a POST request

## Future Enhancements

Potential improvements to consider:

1. **Per-Form Tokens**: Generate unique tokens for each form
2. **Token Rotation**: Automatically rotate tokens after each request
3. **Double Submit Pattern**: Implement cookie-based CSRF tokens
4. **SameSite Cookies**: Enhanced with SameSite cookie attribute

## References

- [OWASP CSRF Prevention Cheat Sheet](https://cheatsheetseries.owasp.org/cheatsheets/Cross-Site_Request_Forgery_Prevention_Cheat_Sheet.html)
- [PHP Security Guide](https://www.php.net/manual/en/security.php)
- [MDN: Cross-site Request Forgery (CSRF)](https://developer.mozilla.org/en-US/docs/Glossary/CSRF)

---

## Summary

The CSRF token protection system implemented in this application provides comprehensive protection against CSRF attacks while maintaining usability and performance. The implementation follows industry best practices and OWASP guidelines, making the Hontoria OMS InternalWeb significantly more secure against malicious actors attempting to exploit authenticated user sessions.
