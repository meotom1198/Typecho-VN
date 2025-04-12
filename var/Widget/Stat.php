<?php

namespace Widget;

if (!defined('__TYPECHO_ROOT_DIR__')) {
    exit;
}

/**
 * Thống kê toàn bộ website
 *
 * @property-read int $allMemberNum
 * @property-read int $publishedPostsNum
 * @property-read int $waitingPostsNum
 * @property-read int $draftPostsNum
 * @property-read int $myPublishedPostsNum
 * @property-read int $myWaitingPostsNum
 * @property-read int $myDraftPostsNum
 * @property-read int $currentPublishedPostsNum
 * @property-read int $currentWaitingPostsNum
 * @property-read int $currentDraftPostsNum
 * @property-read int $publishedPagesNum
 * @property-read int $draftPagesNum
 * @property-read int $publishedCommentsNum
 * @property-read int $waitingCommentsNum
 * @property-read int $spamCommentsNum
 * @property-read int $myPublishedCommentsNum
 * @property-read int $myWaitingCommentsNum
 * @property-read int $mySpamCommentsNum
 * @property-read int $currentCommentsNum
 * @property-read int $currentPublishedCommentsNum
 * @property-read int $currentWaitingCommentsNum
 * @property-read int $currentSpamCommentsNum
 * @property-read int $categoriesNum
 * @property-read int $tagsNum
 */
class Stat extends Base
{
    /**
     * @param int $components
     */
    protected function initComponents(int &$components)
    {
        $components = self::INIT_USER;
    }

    /**
     * Lấy số lượng bài viết đã đăng
     *
     * @return integer
     */
    protected function ___publishedPostsNum(): int
    {
        return $this->db->fetchObject($this->db->select(['COUNT(cid)' => 'num'])
            ->from('table.contents')
            ->where('table.contents.type = ?', 'post')
            ->where('table.contents.status = ?', 'publish'))->num;
    }

    /**
     * Lấy số lượng bài viết chờ duyệt
     *
     * @return integer
     */
    protected function ___waitingPostsNum(): int
    {
        return $this->db->fetchObject($this->db->select(['COUNT(cid)' => 'num'])
            ->from('table.contents')
            ->where('table.contents.type = ? OR table.contents.type = ?', 'post', 'post_draft')
            ->where('table.contents.status = ?', 'waiting'))->num;
    }

    /**
     * Lấy số lượng bài viết đang nháp
     *
     * @return integer
     */
    protected function ___draftPostsNum(): int
    {
        return $this->db->fetchObject($this->db->select(['COUNT(cid)' => 'num'])
            ->from('table.contents')
            ->where('table.contents.type = ?', 'post_draft'))->num;
    }

    /**
     * Lấy số lượng bài viết được xuất bản bởi người dùng hiện tại
     *
     * @return integer
     */
    protected function ___myPublishedPostsNum(): int
    {
        return $this->db->fetchObject($this->db->select(['COUNT(cid)' => 'num'])
            ->from('table.contents')
            ->where('table.contents.type = ?', 'post')
            ->where('table.contents.status = ?', 'publish')
            ->where('table.contents.authorId = ?', $this->user->uid))->num;
    }

    /**
     * Lấy số lượng bài viết được người dùng hiện tại xem xét
     *
     * @return integer
     */
    protected function ___myWaitingPostsNum(): int
    {
        return $this->db->fetchObject($this->db->select(['COUNT(cid)' => 'num'])
            ->from('table.contents')
            ->where('table.contents.type = ? OR table.contents.type = ?', 'post', 'post_draft')
            ->where('table.contents.status = ?', 'waiting')
            ->where('table.contents.authorId = ?', $this->user->uid))->num;
    }

    /**
     * Lấy số lượng bài viết nháp của người dùng hiện tại
     *
     * @return integer
     */
    protected function ___myDraftPostsNum(): int
    {
        return $this->db->fetchObject($this->db->select(['COUNT(cid)' => 'num'])
            ->from('table.contents')
            ->where('table.contents.type = ?', 'post_draft')
            ->where('table.contents.authorId = ?', $this->user->uid))->num;
    }

    /**
     * Lấy số lượng bài viết được xuất bản bởi người dùng hiện tại
     *
     * @return integer
     */
    protected function ___currentPublishedPostsNum(): int
    {
        return $this->db->fetchObject($this->db->select(['COUNT(cid)' => 'num'])
            ->from('table.contents')
            ->where('table.contents.type = ?', 'post')
            ->where('table.contents.status = ?', 'publish')
            ->where('table.contents.authorId = ?', $this->request->filter('int')->uid))->num;
    }

    /**
     * Lấy số lượng bài viết được người dùng hiện tại xem xét
     *
     * @return integer
     */
    protected function ___currentWaitingPostsNum(): int
    {
        return $this->db->fetchObject($this->db->select(['COUNT(cid)' => 'num'])
            ->from('table.contents')
            ->where('table.contents.type = ? OR table.contents.type = ?', 'post', 'post_draft')
            ->where('table.contents.status = ?', 'waiting')
            ->where('table.contents.authorId = ?', $this->request->filter('int')->uid))->num;
    }

    /**
     * Lấy số lượng bài viết nháp của người dùng hiện tại
     *
     * @return integer
     */
    protected function ___currentDraftPostsNum(): int
    {
        return $this->db->fetchObject($this->db->select(['COUNT(cid)' => 'num'])
            ->from('table.contents')
            ->where('table.contents.type = ?', 'post_draft')
            ->where('table.contents.authorId = ?', $this->request->filter('int')->uid))->num;
    }

    /**
     * Lấy số trang đã xuất bản
     *
     * @return integer
     */
    protected function ___publishedPagesNum(): int
    {
        return $this->db->fetchObject($this->db->select(['COUNT(cid)' => 'num'])
            ->from('table.contents')
            ->where('table.contents.type = ?', 'page')
            ->where('table.contents.status = ?', 'publish'))->num;
    }

    /**
     * Lấy số trang nháp
     *
     * @return integer
     */
    protected function ___draftPagesNum(): int
    {
        return $this->db->fetchObject($this->db->select(['COUNT(cid)' => 'num'])
            ->from('table.contents')
            ->where('table.contents.type = ?', 'page_draft'))->num;
    }

    /**
     * Lấy số lượng bình luận hiện đang hiển thị
     *
     * @return integer
     */
    protected function ___publishedCommentsNum(): int
    {
        return $this->db->fetchObject($this->db->select(['COUNT(coid)' => 'num'])
            ->from('table.comments')
            ->where('table.comments.status = ?', 'approved'))->num;
    }

    /**
     * Lấy số lượng bình luận hiện đang chờ xem xét
     *
     * @return integer
     */
    protected function ___waitingCommentsNum(): int
    {
        return $this->db->fetchObject($this->db->select(['COUNT(coid)' => 'num'])
            ->from('table.comments')
            ->where('table.comments.status = ?', 'waiting'))->num;
    }

    /**
     * Lấy số lượng bình luận spam hiện tại
     *
     * @return integer
     */
    protected function ___spamCommentsNum(): int
    {
        return $this->db->fetchObject($this->db->select(['COUNT(coid)' => 'num'])
            ->from('table.comments')
            ->where('table.comments.status = ?', 'spam'))->num;
    }

    /**
     * Lấy số lượng bình luận được hiển thị bởi người dùng hiện tại
     *
     * @return integer
     */
    protected function ___myPublishedCommentsNum(): int
    {
        return $this->db->fetchObject($this->db->select(['COUNT(coid)' => 'num'])
            ->from('table.comments')
            ->where('table.comments.status = ?', 'approved')
            ->where('table.comments.ownerId = ?', $this->user->uid))->num;
    }

    /**
     * Lấy số lượng bình luận được người dùng hiện tại xem xét
     *
     * @return integer
     */
    protected function ___myWaitingCommentsNum(): int
    {
        return $this->db->fetchObject($this->db->select(['COUNT(coid)' => 'num'])
            ->from('table.comments')
            ->where('table.comments.status = ?', 'waiting')
            ->where('table.comments.ownerId = ?', $this->user->uid))->num;
    }

    /**
     * Lấy số lượng bình luận spam của người dùng hiện tại
     *
     * @return integer
     */
    protected function ___mySpamCommentsNum(): int
    {
        return $this->db->fetchObject($this->db->select(['COUNT(coid)' => 'num'])
            ->from('table.comments')
            ->where('table.comments.status = ?', 'spam')
            ->where('table.comments.ownerId = ?', $this->user->uid))->num;
    }

    /**
     * Lấy số lượng bình luận về bài viết hiện tại
     *
     * @return integer
     */
    protected function ___currentCommentsNum(): int
    {
        return $this->db->fetchObject($this->db->select(['COUNT(coid)' => 'num'])
            ->from('table.comments')
            ->where('table.comments.cid = ?', $this->request->filter('int')->cid))->num;
    }

    /**
     * Lấy số lượng bình luận hiển thị trên bài viết hiện tại
     *
     * @return integer
     */
    protected function ___currentPublishedCommentsNum(): int
    {
        return $this->db->fetchObject($this->db->select(['COUNT(coid)' => 'num'])
            ->from('table.comments')
            ->where('table.comments.status = ?', 'approved')
            ->where('table.comments.cid = ?', $this->request->filter('int')->cid))->num;
    }

    /**
     * Lấy số lượng bình luận đang chờ xem xét cho bài viết hiện tại
     *
     * @return integer
     */
    protected function ___currentWaitingCommentsNum(): int
    {
        return $this->db->fetchObject($this->db->select(['COUNT(coid)' => 'num'])
            ->from('table.comments')
            ->where('table.comments.status = ?', 'waiting')
            ->where('table.comments.cid = ?', $this->request->filter('int')->cid))->num;
    }

    /**
     * Lấy số lượng bình luận spam trên bài viết hiện tại
     *
     * @return integer
     */
    protected function ___currentSpamCommentsNum(): int
    {
        return $this->db->fetchObject($this->db->select(['COUNT(coid)' => 'num'])
            ->from('table.comments')
            ->where('table.comments.status = ?', 'spam')
            ->where('table.comments.cid = ?', $this->request->filter('int')->cid))->num;
    }

    /**
     * Lấy số lượng danh mục
     *
     * @return integer
     */
    protected function ___categoriesNum(): int
    {
        return $this->db->fetchObject($this->db->select(['COUNT(mid)' => 'num'])
            ->from('table.metas')
            ->where('table.metas.type = ?', 'category'))->num;
    }

    /**
     * Lấy số lượng thẻ
     *
     * @return integer
     */
    protected function ___tagsNum(): int
    {
        return $this->db->fetchObject($this->db->select(['COUNT(mid)' => 'num'])
            ->from('table.metas')
            ->where('table.metas.type = ?', 'tag'))->num;
    }



    /**
     * Lấy số lượng administrator
     *
     * @return integer
     */
    protected function ___administratorNum(): int
    {
        return $this->db->fetchObject($this->db->select(['COUNT(uid)' => 'num'])
            ->from('table.users')
            ->where('table.users.group = ?', 'administrator'))->num;
    }
    
    /**
     * Lấy số lượng editor
     *
     * @return integer
     */
    protected function ___editorNum(): int
    {
        return $this->db->fetchObject($this->db->select(['COUNT(uid)' => 'num'])
            ->from('table.users')
            ->where('table.users.group = ?', 'editor'))->num;
    }
    
    /**
     * Lấy số lượng contributor
     *
     * @return integer
     */
    protected function ___contributorNum(): int
    {
        return $this->db->fetchObject($this->db->select(['COUNT(uid)' => 'num'])
            ->from('table.users')
            ->where('table.users.group = ?', 'contributor'))->num;
    }
    
    /**
     * Lấy số lượng subscriber
     *
     * @return integer
     */
    protected function ___subcriberNum(): int
    {
        return $this->db->fetchObject($this->db->select(['COUNT(uid)' => 'num'])
            ->from('table.users')
            ->where('table.users.group = ?', 'subscriber'))->num;
    }
    
    /**
     * Lấy tổng tổng số lượng user từ các nhóm: administrator, editor, contributor, subscriber
     *
     * @return int
     */
    protected function ___totalUserNum(): int
    {
    return $this->___administratorNum()
         + $this->___editorNum()
         + $this->___contributorNum()
         + $this->___subcriberNum();
    }

    /**
     * Lấy tổng số lượng file đã upload
     *
     * @return integer
     */
    protected function ___filesNum(): int
    {
        return $this->db->fetchObject($this->db->select(['COUNT(cid)' => 'num'])
            ->from('table.contents')
            ->where('table.contents.type = ?', 'attachment'))->num;
    }

}
