<?php

namespace App\Order\Models;

class Order
{
    public int $id;
    public int $user_id;
    public string $description;
    public float $value;
    public string $currency;
    public string $created_at;
    public string $updated_at;
}