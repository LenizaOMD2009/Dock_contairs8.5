<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class ItemSale extends AbstractMigration
{
   
    public function change(): void
    {
        $table = $this->table('item_sale', ['id' => false, 'primary_key' => ['id']]);
        $table->addColumn('id', 'biginteger', ['identity' => true, 'null' => false])
            ->addColumn('produto_id', 'biginteger')
            ->addColumn('quantidade', 'integer')
            ->addColumn('preco_unitario', 'decimal', ['precision' => 18, 'scale' => 2])
            ->addColumn('preco_total', 'decimal', ['precision' => 18, 'scale' => 2])
            ->addColumn('criado_em', 'datetime', ['default' => 'CURRENT_TIMESTAMP'])
            ->addColumn('atualizado_em', 'datetime', ['null' => true])
            ->create();       
    }
}
