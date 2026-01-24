<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class InstallmentSale extends AbstractMigration
{
   
    public function change(): void
    {
        $table = $this->table('installment_sale', ['id' => false, 'primary_key' => ['id']]);
        $table->addColumn('id', 'biginteger', ['identity' => true, 'null' => false])

    }
}
