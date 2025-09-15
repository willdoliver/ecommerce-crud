<?php

use Phinx\Seed\AbstractSeed;

class AdminUserSeeder extends AbstractSeed
{
    public function run(): void
    {
        $adminName = 'Admin User';
        $adminEmail = 'admin@ecommerce.com';
        $adminPassword = 'supersecret';
        $usersTable = $this->table('users');

        $existingUser = $this->fetchRow("SELECT * FROM users WHERE email = '{$adminEmail}'");

        if (!$existingUser) {
            $adminData = [
                'name'      => $adminName,
                'email'     => $adminEmail,
                'password'  => password_hash($adminPassword, PASSWORD_BCRYPT),
            ];

            $usersTable->insert($adminData)->saveData();

            $this->getOutput()->writeln("Usuário admin '{$adminEmail}' criado com sucesso");
        } else {
            $this->getOutput()->writeln("Usuário admin '{$adminEmail}' já existe");
        }
    }
}