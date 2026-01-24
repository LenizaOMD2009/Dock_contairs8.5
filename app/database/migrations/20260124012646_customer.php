<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class Customer extends AbstractMigration
{
   
    public function change(): void
    {
        $table = $this->table('customer', ['id' => false, 'primary_key' => ['id']]);
        $table->addColumn('id', 'biginteger', ['identity' => true, 'null' => false])

    }
}
