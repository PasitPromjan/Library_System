<div id="bookInfoModal" class="modal fade">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="bookTitle"></h5>
                <button type="button" class="btn-close text-white" data-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <table class="table table-bordered table-striped table-hover">
                    <tbody>
                        <tr class="align-middle">
                            <td class="fw-bold">รหัสหนังสือ</td>
                            <td>
                                <p class="book-info-text m-0"></p>
                            </td>
                        </tr>
                        <tr class="align-middle">
                            <td class="fw-bold">ISBN</td>
                            <td>
                                <p class="book-info-text m-0"></p>
                            </td>
                        </tr>
                        <tr class="align-middle">
                            <td class="fw-bold">ชื่อหนังสือ</td>
                            <td>
                                <p class="book-info-text m-0"></p>
                            </td>
                        </tr>
                        <tr class="align-middle">
                            <td class="fw-bold">หมวดหมู่</td>
                            <td>
                                <p class="book-info-text m-0"></p>
                            </td>
                        </tr>
                        <tr class="align-middle">
                            <td class="fw-bold">ผู้แต่ง</td>
                            <td>
                                <p class="book-info-text m-0"></p>
                            </td>
                        </tr>
                        <tr class="align-middle">
                            <td class="fw-bold">ครั้งที่พิมพ์</td>
                            <td>
                                <p class="book-info-text m-0"></p>
                            </td>
                        </tr>
                        <tr class="align-middle">
                            <td class="fw-bold">สำนักพิมพ์</td>
                            <td>
                                <p class="book-info-text m-0"></p>
                            </td>
                        </tr>
                        <tr class="align-middle">
                            <td class="fw-bold">ปีที่พิมพ์</td>
                            <td>
                                <p class="book-info-text m-0"></p>
                            </td>
                        </tr>
                        <tr class="align-middle">
                            <td class="fw-bold">จำนวนหน้า</td>
                            <td>
                                <p class="book-info-text m-0"></p>
                            </td>
                        </tr>
                        <tr class="align-middle">
                            <td class="fw-bold">ราคา</td>
                            <td>
                                <p class="book-info-text m-0"></p>
                            </td>
                        </tr>
                        <tr class="align-middle">
                            <td class="fw-bold">รูปภาพ</td>
                            <td>
                                <img id="bookInfoImage" class="img-fluid book-img" alt="Book Image">
                            </td>
                        </tr>
                        <tr class="align-middle">
                            <td class="fw-bold">วันที่เพิ่ม</td>
                            <td>
                                <p class="book-info-text m-0"></p>
                            </td>
                        </tr>
                        <tr class="align-middle">
                            <td class="fw-bold">แก้ไขล่าสุด</td>
                            <td>
                                <p class="book-info-text m-0"></p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" data-dismiss="modal">ปิด</button>
            </div>
        </div>
    </div>
</div>


<script>
    // const bookdetailModal = new bootstrap.Modal($('#bookdetail-modal'))
    // const bookViewdetail = $('.book-viewdetail')
    // const editDetailbook = $('#edit-detailbook')
    // bookViewdetail.click(function() {

    //     const d = JSON.parse(atob(($(this).attr('data-detail'))))

    //     const {
    //         book_auther,
    //         book_category,
    //         book_edition,
    //         book_id,
    //         book_img,
    //         book_insert,
    //         book_pagecount,
    //         book_price,
    //         book_publisher,
    //         book_pubyear,
    //         book_update,
    //         bookname,
    //         id,
    //     } = d



    //     $("#book-title").text(bookname)
    //     $("#book-id").text(book_id)
    //     $("#text-bookname").text(bookname)
    //     $("#text-bookcategory").text(book_category)
    //     $("#text-auther").text(book_auther)
    //     $("#text-edition").text(book_edition)
    //     $("#text-publisher").text(book_publisher)
    //     $("#text-pubyear").text(book_publisher)
    //     $("#text-pagecount").text(book_pagecount)
    //     $("#text-bookprice").text(book_price)
    //     $("#text-bookcreate").text(book_insert)
    //     $("#text-bookupdate").text(book_update)
    //     $("#book-img").attr('src', `./book_img/${book_img}`)
    //     editDetailbook.attr('data-book-id', book_id)

    //     bookdetailModal.show()
    // })

    // editDetailbook.click(function() {
    //     const bId = $(this).attr('data-book-id')
    //     console.log(bId)

    //     const r = `./?ptype=editbook&bid=${bId}`
    //     window.location.assign(r)
    // })
</script>