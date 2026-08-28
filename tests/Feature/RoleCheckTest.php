<?php

use Spatie\Permission\Models\Role;

test('roles exists after seeding', function () {
    $names = Role::pluck('name');
    expect($names)->toContain('student');
});
