<?php
/**
 * Template part for displaying posts
 */
?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
    <header class="entry-header">
        <?php
        if (is_singular()) :
            the_title('<h1 class="entry-title">', '</h1>');
        else :
            the_title('<h2 class="entry-title"><a href="' . esc_url(get_permalink()) . '" rel="bookmark">', '</a></h2>');
        endif;

        if ('post' === get_post_type()) :
            ?>
            <div class="entry-meta">
                <?php
                printf(
                    __('Publié le %s', 'elkair'),
                    '<time datetime="' . esc_attr(get_the_date('c')) . '">' . esc_html(get_the_date()) . '</time>'
                );
                ?>
            </div>
        <?php endif; ?>
    </header>

    <?php if (has_post_thumbnail() && !is_singular()) : ?>
        <div class="post-thumbnail">
            <a href="<?php the_permalink(); ?>">
                <?php the_post_thumbnail('large'); ?>
            </a>
        </div>
    <?php endif; ?>

    <div class="entry-content">
        <?php
        if (is_singular()) :
            the_content();
        else :
            the_excerpt();
            ?>
            <a href="<?php the_permalink(); ?>" class="read-more">
                <?php _e('Lire la suite', 'elkair'); ?>
            </a>
        <?php
        endif;
        ?>
    </div>

    <?php if (is_singular()) : ?>
        <footer class="entry-footer">
            <?php
            $categories = get_the_category();
            if ($categories) :
                ?>
                <div class="entry-categories">
                    <?php
                    printf(
                        __('Catégories: %s', 'elkair'),
                        get_the_category_list(', ')
                    );
                    ?>
                </div>
            <?php endif; ?>

            <?php
            $tags = get_the_tags();
            if ($tags) :
                ?>
                <div class="entry-tags">
                    <?php
                    printf(
                        __('Tags: %s', 'elkair'),
                        get_the_tag_list('', ', ')
                    );
                    ?>
                </div>
            <?php endif; ?>
        </footer>
    <?php endif; ?>
</article>
