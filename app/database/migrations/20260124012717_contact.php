<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class Contact extends AbstractMigration
{
    
    public function change(): void
    {
        $table = $this->table('contact', ['id' => false, 'primary_key' => ['id']]);
        $table->addColumn('id', 'biginteger', ['identity' => true, 'null' => false])
        ->addColumn('id_users', 'biginteger')
        ->addColumn('id_customer', 'biginteger')
        ->addColumn('id_company', 'biginteger')
        ->addColumn('documento', 'string', ['limit' => 30])
        ->addColumn('email', 'string', ['limit' => 150])
        ->addColumn('telefone', 'string', ['limit' => 30])
        ->addColumn('data_cadastro', 'datetime', ['null' => true, 'default' => 'CURRENT_TIMESTAMP'])
        ->addColumn('data_atualizacao', 'datetime', ['null' => true, 'default' => 'CURRENT_TIMESTAMP'])
        ->create();
    }
}
