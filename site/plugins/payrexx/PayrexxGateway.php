<?php
class PayrexxGateway {
    
    private $instanceName;
    private $apiSecret;
    private $payrexx;
    
    public function __construct($instanceName, $apiSecret) {
        $this->instanceName = $instanceName;
        $this->apiSecret = $apiSecret;
        require_once __DIR__ . '/lib/payrexx-php/autoload.php';
        $this->payrexx = new \Payrexx\Payrexx($instanceName, $apiSecret);
    }
    
    public function createPayment($cartData, $customerData, $deliveryData) {
        $total = $this->calculateTotal($cartData, $deliveryData);
        
        $payment = new \Payrexx\Models\Request\Payment();
        $payment->setAmount($total * 100); // Cent
        $payment->setCurrency('CHF');
        $payment->setReferenceId('deli-order-' . time());
        $payment->setCustomerFirstname($customerData['firstName']);
        $payment->setCustomerLastname($customerData['lastName']);
        $payment->setCustomerEmail($customerData['email']);
        $payment->setCustomerPhone($customerData['phone'] ?? '');
        $payment->setDescription('La Rotonda Delivery Order');
        
        $response = $this->payrexx->create($payment);
        return [
            'success' => true,
            'paymentUrl' => $response->getRedirectUrl(),
            'transactionId' => $response->getId()
        ];
    }
    
    private function calculateTotal($cart, $delivery) {
        $cartTotal = array_sum(array_map(function($item) {
            return $item['price'] * $item['quantity'];
        }, $cart));
        return $cartTotal + ($delivery['price'] ?? 0);
    }
}
