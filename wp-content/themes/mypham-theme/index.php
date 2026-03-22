<?php
/**
 * The main template file
 * 
 * @package MyPhamTheme
 */

get_header();
?>

<div class="main-content">
    <div class="container">
        <div class="content-wrapper">
            <div class="primary-content">
                <?php if (have_posts()) : ?>
                    <div class="posts-list">
                        <?php while (have_posts()) : the_post(); ?>
                            <article id="post-<?php the_ID(); ?>" <?php post_class('post-item'); ?>>
                                <?php if (has_post_thumbnail()) : ?>
                                    <div class="post-thumbnail">
                                        <a href="<?php the_permalink(); ?>">
                                            <?php the_post_thumbnail('medium'); ?>
                                        </a>
                                    </div>
                                <?php endif; ?>
                                
                                <div class="post-content">
                                    <h2 class="post-title">
                                        <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                                    </h2>
                                    
                                    <div class="post-meta">
                                        <span class="post-date">
                                            <i class="far fa-calendar-alt"></i> <?php echo get_the_date(); ?>
                                        </span>
                                        <span class="post-author">
                                            <i class="far fa-user"></i> <?php the_author(); ?>
                                        </span>
                                        <span class="post-categories">
                                            <i class="far fa-folder-open"></i> <?php the_category(', '); ?>
                                        </span>
                                        <span class="post-comments">
                                            <i class="far fa-comment"></i> <?php comments_number('0', '1', '%'); ?>
                                        </span>
                                    </div>
                                    
                                    <div class="post-excerpt">
                                        <?php the_excerpt(); ?>
                                    </div>
                                    
                                    <a href="<?php the_permalink(); ?>" class="read-more">
                                        Đọc thêm <i class="fas fa-arrow-right"></i>
                                    </a>
                                </div>
                            </article>
                        <?php endwhile; ?>
                    </div>
                    
                    <div class="pagination">
                        <?php
                        the_posts_pagination(array(
                            'mid_size' => 2,
                            'prev_text' => '<i class="fas fa-chevron-left"></i>',
                            'next_text' => '<i class="fas fa-chevron-right"></i>',
                            'screen_reader_text' => ' '
                        ));
                        ?>
                    </div>
                    
                <?php else : ?>
                    <div class="no-posts">
                        <i class="fas fa-inbox fa-3x"></i>
                        <h3>Không tìm thấy bài viết</h3>
                        <p>Hiện tại chưa có bài viết nào. Vui lòng quay lại sau.</p>
                        <a href="<?php echo home_url(); ?>" class="btn btn-primary">Về trang chủ</a>
                    </div>
                <?php endif; ?>
            </div>
            
            <aside class="sidebar">
                <?php get_sidebar(); ?>
            </aside>
        </div>
    </div>
</div>

<?php
get_footer();
?>