<?php 
if (!isset($items) || !is_array($items))return;
?>
<div class="airlines-boxes-container">
    <?php foreach ($items as $item): ?>
        <div class="airline-box">
            <div class="airline-box-top">
                <img src="<?php echo $item['image']; ?>" alt="<?php echo $item['name']; ?>" class="airline-logo">
            </div>
            <div class="airline-box-middle">
                <div class="airline-name"><?php echo $item['name']; ?></div>
            </div>
            <div class="airline-box-bottom">
                <div class="airline-info">
                    <div>Ticket Sold: <?php echo $item['tickets_sold']; ?></div>
                    <div>Revenue: <?php echo $item['revenue']; ?></div>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>
