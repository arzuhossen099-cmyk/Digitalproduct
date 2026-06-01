<?php
require_once 'includes/header.php';
require_once 'includes/auth_check.php';
?>

<div class="card shadow-sm mb-4">
    <div class="card-body">
        <h5 class="card-title">Support</h5>
        <p class="text-muted">If you have any issues, please contact us on WhatsApp or Telegram.</p>

        <div class="d-grid gap-3">
            <a href="https://wa.me/01700000000" class="btn btn-success py-3">
                <i class="fab fa-whatsapp me-2"></i> Contact on WhatsApp
            </a>
            <a href="https://t.me/example" class="btn btn-info text-white py-3">
                <i class="fab fa-telegram me-2"></i> Join Telegram Channel
            </a>
        </div>
    </div>
</div>

<div class="card shadow-sm">
    <div class="card-body">
        <h5 class="card-title">FAQ</h5>
        <div class="accordion accordion-flush" id="faqAccordion">
            <div class="accordion-item">
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq1">
                        How to deposit money?
                    </button>
                </h2>
                <div id="faq1" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                    <div class="accordion-body">
                        Go to Deposit section, copy our number, send money via bKash/Nagad, and submit the Transaction ID.
                    </div>
                </div>
            </div>
            <div class="accordion-item">
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq2">
                        How long it takes to approve deposit?
                    </button>
                </h2>
                <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                    <div class="accordion-body">
                        Usually it takes 5-30 minutes for our team to verify and approve.
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
require_once 'includes/bottom_menu.php';
require_once 'includes/footer.php';
?>
