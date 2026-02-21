<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class StockMovement extends AbstractMigration
{
 
    public function change(): void
    {
       $table = $this->table('stock_movement', ['id' => false, 'primary_key' => ['id']]);
        $table->addColumn('id', 'biginteger', ['identity' => true, 'null' => false])
        ->addColumn('id_product', 'biginteger')
        ->addColumn('quantidade_entrada', 'integer', ['default' => 0])
        ->addColumn('quantidade_saida', 'integer', ['default' => 0])
        ->addColumn('estoque_atual', 'integer', ['default' => 0])
        ->addColumn('data_cadastro', 'datetime', ['null' => true, 'default' => 'CURRENT_TIMESTAMP'])
        ->addColumn('data_atualizacao', 'datetime', ['null' => true, 'default' => 'CURRENT_TIMESTAMP'])
        ->addForeignKey('id_product', 'product', 'id', ['delete'=> 'CASCADE', 'update'=> 'NO ACTION'])
        ->create();
    }
}
