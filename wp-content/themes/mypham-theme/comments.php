<?php
if (post_password_required()) {
    return;
}
?>

<div id="comments" class="comments-area">

    <?php if (have_comments()) : ?>
        <h3 class="comments-title">
            <?php
            printf(_n('%s bình luận', '%s bình luận', get_comments_number(), 'textdomain'),
                number_format_i18n(get_comments_number()));
            ?>
        </h3>

        <ol class="comment-list">
            <?php
            wp_list_comments([
                'style'      => 'ol',
                'short_ping' => true,
                'avatar_size'=> 50,
            ]);
            ?>
        </ol>

    <?php endif; ?>

    <?php
    comment_form([
        'title_reply' => 'Để lại bình luận',
        'label_submit' => 'Gửi bình luận',
        'comment_field' => '<p class="comment-form-comment">
            <textarea name="comment" rows="5" placeholder="Nhập bình luận..." required></textarea>
        </p>',
    ]);
    ?>

</div>