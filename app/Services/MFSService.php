<?php

namespace App\Services;

class MFSService
{

    public function index()
    {
        return "💰 Please enter the amount (e.g. 1000)";
    }

    public function chargeCalculator($amount)
    {
        $amount = floatval($amount);

        // Cash-out charges as decimal rates
        $bkash_fee_rate = 0.0185;  // 1.85%
        $nagad_fee_rate = 0.0149;  // 1.49%
        $rocket_fee_rate = 0.015;  // 1.50%

        // Calculate charge on the base amount
        $bkash_charge = $amount * $bkash_fee_rate;
        $bkash_total_send = $amount + $bkash_charge;

        $nagad_charge = $amount * $nagad_fee_rate;
        $nagad_total_send = $amount + $nagad_charge;

        $rocket_charge = $amount * $rocket_fee_rate;
        $rocket_total_send = $amount + $rocket_charge;

        $response = "To send ৳" . number_format($amount, 2) . " and cover charges:\n\n";

        $response .= "📲 bKash:\n";
        $response .= " Charge: ৳" . number_format($bkash_charge, 2) . "\n";
        $response .= " Total to send: ৳" . number_format($bkash_total_send, 2) . "\n\n";

        $response .= "📲 Nagad:\n";
        $response .= " Charge: ৳" . number_format($nagad_charge, 2) . "\n";
        $response .= " Total to send: ৳" . number_format($nagad_total_send, 2) . "\n\n";

        $response .= "📲 Rocket:\n";
        $response .= " Charge: ৳" . number_format($rocket_charge, 2) . "\n";
        $response .= " Total to send: ৳" . number_format($rocket_total_send, 2);

        return $response;
    }


}
