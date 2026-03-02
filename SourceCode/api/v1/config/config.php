// Contract says 24h token, Sprint 4
<?php
declare(strict_types=1);

return [
    "env" => "dev",
    "jwt_secret" => "CHANGE_THIS_TO_A_LONG_RANDOM_SECRET",
    "jwt_exp_seconds" => 86400, // 24h
];