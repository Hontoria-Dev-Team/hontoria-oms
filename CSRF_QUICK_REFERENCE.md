# CSRF Implementation - Quick Reference Guide

## For End Users

### What is CSRF Protection?
CSRF (Cross-Site Request Forgery) protection prevents malicious websites from making unwanted changes to your account when you're logged in.

### How Will I Notice It?
You won't! CSRF protection works behind the scenes. Your forms will continue to work normally.

### What If I Get an Error?
**Error Message**: "Security validation failed. Please try again."

**What to do**:
1. Refresh your page
2. Try your action again
3. If it keeps happening, contact support

---

## For Developers

### Scenario 1: Add CSRF Token to a New Static Form

**File**: `InternalWeb/Views/YourPage/Page.php`

```php
<form method="POST" action="index.php?page=yourpage&action=save">
    <?php echo CsrfM::getTokenField(); ?>
    
    <input type="text" name="fieldName" required>
    <input type="submit" value="Save">
</form>
```

**That's it!** The token is automatically included.

---

### Scenario 2: Add CSRF Protection to a New Action

**File**: `InternalWeb/Public/index.php`

Find the `ValidateCsrfToken()` function and add your page/action:

```php
$protectedActions = [
    'yourpage' => ['save', 'delete', 'update'],  // Add this line
    'login' => ['authenticate'],
    // ... rest of the actions
];
```

---

### Scenario 3: Create a Dynamic Form in JavaScript

```javascript
// Create form elements
const form = document.createElement('form');
form.method = 'POST';
form.action = 'index.php?page=orders&action=create';

// Add input fields
const input = document.createElement('input');
input.type = 'text';
input.name = 'orderName';
form.appendChild(input);

// ADD CSRF TOKEN - This is the important part!
addCsrfTokenToForm(form);

// Now you can submit the form
document.body.appendChild(form);
// form.submit(); // if auto-submit needed
```

---

### Scenario 4: Manually Add Token to Confirmation Dialog

**File**: `InternalWeb/Views/.Components/ConfirmationBox.php`

The token is already included! Just make sure to call:

```javascript
ensureCsrfTokenInConfirmationForm();
```

Before setting the form action. (This is already done in `ConfirmationBox.js`)

---

### Scenario 5: Debug: Check If Token Exists

**In Browser DevTools Console**:

```javascript
// Should return the token string
getCsrfToken()

// Should return the token value (same as above)
document.querySelector('input[name="_csrf_token"]').value
```

**Expected**: Both should return a 64-character hex string

---

### Scenario 6: Check Protected Actions

**File**: `InternalWeb/Public/index.php`

Search for `$protectedActions` array. It currently protects:

```
login → authenticate
staff → setRoles, createFinal, changeRolePermissions, ...
account → rename, updateContacts, changePassword, ...
services → createService, deleteService, ...
orders → createFinal, changeDeadline, ...
tasks → assignToTask, ...
inventory → updateRecord, ...
sales → createInflowRecord, ...
```

If your action is not in this list, add it!

---

### Scenario 7: Manually Verify Token in Controller (Optional)

If you need to manually verify a token (already done automatically):

```php
try {
    CsrfM::validateToken();
    // Token is valid, continue processing
} catch (Exception $e) {
    // Token is invalid
    error_log($e->getMessage());
    die('Invalid request');
}
```

---

### Scenario 8: Regenerate Token After Sensitive Operation

```php
// After password change, login, etc.
CsrfM::regenerateToken();
```

This creates a brand new token for enhanced security.

---

## Files Changed at a Glance

| File | Change | Importance |
|------|--------|------------|
| `InternalWeb/Public/index.php` | Added CSRF init & validation | 🔴 Critical |
| `InternalWeb/Middleware/CsrfM.php` | **New file** | 🔴 Critical |
| `InternalWeb/.JS/CsrfHandler.js` | **New file** | 🟡 Important |
| All POST forms | Added token field | 🟡 Important |
| ConfirmationBox.php | Added token & JS update | 🟡 Important |
| Script includes | Added CsrfHandler.js | 🟡 Important |

---

## Testing Checklist

Use this to verify CSRF protection is working:

- [ ] Login form works normally
- [ ] Staff creation form works normally
- [ ] Order creation form works normally
- [ ] Account settings can be updated normally
- [ ] In DevTools, I can see `_csrf_token` field in POST requests
- [ ] I can see the token value changes after login
- [ ] If I manually delete the token from HTML, form submission fails
- [ ] If I manually change the token value, form submission fails

---

## Common Questions

### Q: Will this break existing forms?
**A**: No. All existing forms automatically include the token. No form code needs to change (unless it's a new form).

### Q: Do I need to change form IDs or names?
**A**: No. The token field is named `_csrf_token` - don't change this name.

### Q: Can users see the token?
**A**: Yes, but that's OK. Tokens are unique per session and short-lived. Even if seen, they can't be reused on another site.

### Q: What if JavaScript is disabled?
**A**: Static forms still work with the token. Dynamic form creation won't work, but that's expected.

### Q: Can I use CSRF tokens with AJAX?
**A**: Yes. Use `getCsrfToken()` function or read from form data.

### Q: How often are tokens rotated?
**A**: Currently, tokens persist until manually regenerated. You can call `CsrfM::regenerateToken()` for additional security.

### Q: What's the token lifetime?
**A**: Tokens live for the duration of the session. Session expires based on your PHP session settings.

---

## Error Scenarios and Solutions

| Scenario | Error Message | Solution |
|----------|---------------|----------|
| Missing token field | "Security validation failed" | Ensure form includes `CsrfM::getTokenField()` |
| Wrong token value | "Security validation failed" | Don't modify token value |
| Session expired | "Security validation failed" | User needs to log in again |
| Token not in POST | "Security validation failed" | Form must use POST method, not GET |
| Action not protected | (No error, request succeeds) | Add action to `$protectedActions` array |

---

## Reference Links

- **Main Documentation**: See `CSRF_IMPLEMENTATION.md`
- **Code**: `InternalWeb/Middleware/CsrfM.php`
- **JavaScript**: `InternalWeb/.JS/CsrfHandler.js`
- **OWASP Guide**: [CSRF Prevention Cheat Sheet](https://cheatsheetseries.owasp.org/cheatsheets/Cross-Site_Request_Forgery_Prevention_Cheat_Sheet.html)

---

## Support

**For Questions**:
- Check the detailed `CSRF_IMPLEMENTATION.md` file
- Review code comments in `CsrfM.php`
- Look at example forms in the Views folder

**For Issues**:
- Check PHP error logs for CSRF validation failures
- Use Browser DevTools to verify token presence
- Test with curl/Postman if needed

---

**Last Updated**: May 5, 2026
**CSRF Implementation Status**: ✅ Complete and Active
