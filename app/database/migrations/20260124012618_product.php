<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class Product extends AbstractMigration
{
  
    public function change(): void
    {
        $table = $this->table('product', ['id' => false, 'primary_key' => ['id']]);
        $table->addColumn('id', 'biginteger', ['identity' => true, 'null' => false])
            ->addColumn('id_fornecedor', 'biginteger')
            ->addColumn('nome', 'string', ['limit' => 150])
            ->addColumn('descricao', 'text', ['null' => true])
            ->addColumn('preco', 'decimal', ['precision' => 18, 'scale' => 2])
            ->addColumn('estoque', 'integer')
            ->addColumn('data_cadastro', 'datetime', ['null' => true, 'default' => 'CURRENT_TIMESTAMP'])
            ->addColumn('data_atualizacao', 'datetime', ['null' => true, 'default' => 'CURRENT_TIMESTAMP'])

            ->addForeignKey('id_fornecedor', 'fornecedor', 'id', ['delete'=> 'RESTRICT', 'update'=> 'CASCADE'])
            ->create();
    }
}
