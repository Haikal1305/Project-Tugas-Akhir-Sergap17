<?php

return [
    'default' => 'basic',

    'localizer' => [
        'enabled_register_at_gates' => false
    ],

    // Guard "basic" (ReBAC Murni - Location, dipakai juga untuk view/export laporan patroli)
    'basic' => [
        'model' => [
            'config_type' => 'text',
            'config_file_path' => '', 
            'config_text' => '
[request_definition]
r = sub, obj, act

[policy_definition]
p = role, act

[role_definition]
g = _, _, _

[policy_effect]
e = some(where (p.eft == allow))

[matchers]
m = g(r.sub, p.role, r.obj) && r.act == p.act
',
            'config_url' => ''
        ],
        'adapter' => Lauthz\Adapters\DatabaseAdapter::class,
        'database' => [
            'connection' => '',
            'rules_table' => 'rules',
        ],
        'log' => ['enabled' => false, 'logger' => 'log'],
        'cache' => ['enabled' => false, 'store' => 'default', 'key' => 'rules', 'ttl' => 24 * 60],
    ],

    // GUARD "attendance" (ABAC Murni - Operasional Satpam: clock_in, clock_out, patrol_create)
    'attendance' => [
        'model' => [
            'config_type' => 'text',
            'config_file_path' => '',
            'config_text' => '
[request_definition]
r = sub, obj, act, env

[policy_definition]
p = sub_rule, env_rule, act

[policy_effect]
e = some(where (p.eft == allow))

[matchers]
m = r.act == p.act && eval(p.sub_rule) && eval(p.env_rule)
',
            'config_url' => ''
        ],
        'adapter' => Lauthz\Adapters\DatabaseAdapter::class,
        'database' => [
            'connection' => '',
            'rules_table' => 'attendance_rules',
        ],
        'log' => ['enabled' => false, 'logger' => 'log'],
        'cache' => ['enabled' => false, 'store' => 'default', 'key' => 'attendance_rules', 'ttl' => 24 * 60],
    ],
];