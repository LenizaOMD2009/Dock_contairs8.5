<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class Sale extends AbstractMigration
{

    public function change(): void
    {
        $table = $this->table('sale', ['id' => false, 'primary_key' => ['id']]);
        $table->addColumn('id', 'biginteger', ['identity' => true, 'null' => false])
              ->addColumn('id_customer', 'biginteger')
              ->addColumn('valor_total', 'decimal', ['precision' => 18, 'scale' => 4, 'default' => 0])
              ->addColumn('status', 'boolean', ['default' => 'ABERTA'])
              ->addColumn('data_venda', 'datetime', ['default' => 'CURRENT_TIMESTAMP'])
              ->addColumn('data_cadastro', 'datetime', ['null' => true, 'default' => 'CURRENT_TIMESTAMP'])
              ->addColumn('data_alteracao', 'datetime', ['null' => true, 'default' => 'CURRENT_TIMESTAMP'])

            ->addForeignKey('id_customer', 'customer', 'id', ['delete'=> 'RESTRICT', 'update'=> 'CASCADE'])
            ->create();
    }
}

