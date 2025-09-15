<?php

use Phinx\Migration\AbstractMigration;

class CreateOrdersTable extends AbstractMigration
{
    public function up(): void
    {
        $this->execute("CREATE TYPE currency_type AS ENUM ('BRL', 'USD')");

        $this->execute("
            CREATE TABLE orders (
              id SERIAL PRIMARY KEY,
              user_id INTEGER NOT NULL,
              description VARCHAR(255) NOT NULL,
              value NUMERIC(10, 2) NOT NULL,
              currency currency_type NOT NULL,
              created_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP,
              updated_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP,
              CONSTRAINT fk_user
                FOREIGN KEY(user_id) 
                REFERENCES users(id)
                ON DELETE CASCADE
            )
        ");
    }

    public function down(): void
    {
        $this->table('orders')->drop()->save();
        $this->execute('DROP TYPE currency_type');
    }
}