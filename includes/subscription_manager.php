<?php
/**
 * LeadPress Subscription Manager
 */

class SubscriptionManager {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function activatePlan($user_id, $plan_id) {
        $stmt = $this->pdo->prepare("SELECT * FROM plans WHERE id = ? AND is_active = 1");
        $stmt->execute([$plan_id]);
        $plan = $stmt->fetch();

        if (!$plan) return false;

        try {
            $this->pdo->beginTransaction();

            // Deactivate current active subscriptions
            $stmt = $this->pdo->prepare("UPDATE subscriptions SET status = 'cancelled' WHERE user_id = ? AND status = 'active'");
            $stmt->execute([$user_id]);

            // Create new subscription
            $expires_at = date('Y-m-d H:i:s', strtotime("+{$plan['duration_days']} days"));
            $stmt = $this->pdo->prepare("INSERT INTO subscriptions (user_id, plan_id, status, starts_at, expires_at) VALUES (?, ?, 'active', NOW(), ?)");
            $stmt->execute([$user_id, $plan_id, $expires_at]);

            // Add credits
            $stmt = $this->pdo->prepare("UPDATE users SET credits = credits + ? WHERE id = ?");
            $stmt->execute([$plan['credits_per_month'], $user_id]);

            // Log credits
            $stmt = $this->pdo->prepare("INSERT INTO credits (user_id, amount, type, description) VALUES (?, ?, 'subscription', ?)");
            $stmt->execute([$user_id, $plan['credits_per_month'], "Plan Activation: {$plan['name']}"]);

            $this->pdo->commit();
            return true;
        } catch (Exception $e) {
            $this->pdo->rollBack();
            lp_log("Subscription Activation Error: " . $e->getMessage(), 'ERROR');
            return false;
        }
    }

    public function checkAndRenew() {
        // Implementation for cron-based renewal logic
    }
}
?>
