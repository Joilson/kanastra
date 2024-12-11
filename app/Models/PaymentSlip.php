<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentSlip extends Model
{
    protected $table = 'payment_slips';

    public static function fromCsv(array $csvRow): self
    {
        $entity = new PaymentSlip();
        $entity->name = $csvRow['name'];
        $entity->government_id = $csvRow['governmentId'];
        $entity->email = $csvRow['email'];
        $entity->debit_amount = $csvRow['debtAmount'];
        $entity->debit_due_date = \DateTimeImmutable::createFromFormat('Y-m-d', $csvRow['debtDueDate']);
        $entity->debt_id = $csvRow['debtId'];

        return $entity;
    }
}
