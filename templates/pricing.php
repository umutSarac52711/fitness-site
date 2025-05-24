<!-- Pricing Section Begin -->

<section class="pricing-section spad" id="pricing">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="section-title">
                        <span>Our Plans</span>
                        <h2>Choose your pricing plan</h2>
                    </div>
                </div>
            </div>
            <div class="row justify-content-center">
                <?php
                $stmt = $pdo->query('SELECT * FROM plans ORDER BY price ASC');
                $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

                if ($result && count($result) > 0):
                    foreach ($result as $plan):
                        // If the features are stored as a comma-separated string, split them into an array
                        $featuresList = array_map('trim', explode(',', $plan['features']));
                ?>
                        <div class="col-lg-4 col-md-8">
                            <div class="ps-item">
                                <h3><?= htmlspecialchars($plan['name']); ?></h3>
                                <div class="pi-price">
                                    <h2>₺ <?= htmlspecialchars($plan['price']); ?></h2>
                                    <span><?= htmlspecialchars($plan['duration_weeks']); ?> weeks </span>
                                </div>
                                <ul>
                                    <?php foreach ($featuresList as $feature): ?>
                                        <li><?= htmlspecialchars($feature); ?></li>
                                    <?php endforeach; ?>
                                </ul>
                                <a href="#" class="primary-btn pricing-btn">Enroll now</a>
                                <a href="#" class="thumb-icon"><i class="fa fa-picture-o"></i></a>
                            </div>
                        </div>
                <?php
                    endforeach;
                else:
                    echo "<p>No plans found.</p>";
                endif;?>

            </div>
        </div>
    </section>

<!-- Pricing Section End -->