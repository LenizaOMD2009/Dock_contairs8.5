<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class Sale extends AbstractMigration
{

    public function change(): void
    {
        $table = $this->table('sale', ['id' => false, 'primary_key' => ['id']]);
        $table->addColumn('id', 'biginteger', ['identity' => true, 'null' => false])
            ->addColumn('cliente_id', 'biginteger')
            ->addColumn('empresa_id', 'biginteger')
            ->addColumn('condicao_pagamento_id', 'biginteger')
            ->addColumn('valor_total', 'decimal', ['precision' => 18, 'scale' => 2])
            ->addColumn('status', 'string', ['limit' => 50])
            ->addColumn('data_venda', 'datetime')
            ->addColumn('criado_em', 'datetime', ['default' => 'CURRENT_TIMESTAMP'])
            ->addColumn('atualizado_em', 'datetime', ['null' => true])
            ->create();
    }
}
