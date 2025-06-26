<?php if($announcements->isEmpty()): ?>
<div class="alert alert-info" role="alert">
    No announcement search results found.
</div>
<?php else: ?>
<div class="row">
    <?php $__currentLoopData = $announcements; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $announcement): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title" style="font-size: 20px;">
                        <?php echo e($announcement->subject); ?>

                    </h5>
                    <h6 class="card-subtitle text-muted" style="font-size:12px;">
                        <?php echo e(\Carbon\Carbon::parse($announcement->created_at)->setTimezone('Asia/Manila')->format('F j, Y, g:i a')); ?>

                    </h6>
                    <p class="card-subtitle text-muted mt-2" style="font-size:12px;">
                        To:
                        <?php $__currentLoopData = $announcement->displayEmails; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $email): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php echo e($email); ?><?php if(!$loop->last): ?>
                                ,
                            <?php endif; ?>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <?php if($announcement->moreEmailsCount > 0): ?>
                            and <?php echo e($announcement->moreEmailsCount); ?> more
                        <?php endif; ?>
                    </p>
                </div>
                <div class="card-body">
                    <p class="card-text"><?php echo $announcement->message; ?></p>
                </div>
            </div>
        </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</div>
<?php endif; ?><?php /**PATH /home/u687103837/domains/pupt-farm.com/public_html/resources/views/faculty/announcement-list.blade.php ENDPATH**/ ?>