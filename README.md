## Roles and Permissions

This application comes with a custom, built-in permissions and roles system. This system allows you to assign roles and permissions to users. Users will be have all permissions that are either directly assigned, or assigned via a role.

All functions can take either a string containing the name of the permission/role, or the Permission/Role class. An array can contain all strings, all classes, or a mix of both.

```
// Checking permissions
$user->hasPermission('create-budget'); // Check if the user has the specified permission
$user->hasAnyPermission(['create-budget', 'delete-budget', ...]); // Check if the user has one of the following permissions
$user->hasAllPermissions(['create-budget', 'delete-budget', ...]); // Check if the user has all of the following permissions

// Assigning permissions
$user->assignPermission('create-budget'); // Assign the following permission to the user, even if their role doesn't allow it
$user->assignPermissions(['create-budget', 'delete-budget', ...]); // Assign all the following permissions to the user, even if their role doesn't allow it

// Revoking permissions
$user->assignPermission('create-budget'); // Remove this direct permission (user will still have permission if their role allows it)
$user->assignPermissions(['create-budget', 'delete-budget', ...]); // Remove these direct permissions (user will still have permission if their role allows it)

// Checking roles
$user->hasRole('admin'); // Check if the user has the specified role
$user->hasAnyRole(['admin', 'editor', ...]); // Check if the user has one of the following roles
$user->hasAllRoles(['admin', 'editor', ...]); // Check if the user has all of the following roles

// Assigning roles
$user->assignRole('admin'); // Assign the following role to the user
$user->assignRoles(['admin', 'editor', ...]); // Assign all the following roles to the user

// Revoking roles
$user->removeRole('admin'); // Remove this role
$user->removeRoles(['admin', 'editor', ...]); // Remove these roles

// Assigning permissions to roles
$role->assignPermission('create-budget'); // Assign the following permission to the role
$role->assignPermissions(['create-budget', 'delete-budget', ...]); // Assign all the following permissions to the role

// Revoking permissions from roles
$role->removePermission('create-budget'); // Remove this permission
$role->removePermissions(['create-budget', 'delete-budget', ...]); // Remove these permissions
```
