<div class="modal fade" id="officerModal">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content shadow-lg border border-danger">
            <div class="modal-header bg-gradient-danger text-white">
                <h5 class="modal-title">เพิ่มเจ้าที่ และผู้ดูแล</h5>
                <button type="button" class="btn btn-light" data-dismiss="modal">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
            <div class="modal-body">
                <form action="" id="officerForm">
                    <div class="form-group my-1">
                        <label>ผู้ใช้</label>
                        <input type="text" id="username" class="form-control" placeholder="ป้อนชื่อผู้ใช้งาน">
                    </div>
                    <p class="err-validate text-danger" id="validate-username"></p>
                    <div class="form-group my-1">
                        <label>รหัสผ่าน</label>
                        <div class="input-group">
                            <input type="password" id="password" class="form-control" placeholder="ป้อนรหัสผ่าน">
                            <div class="input-group-append">
                                <button type="button" onclick="obscureText('#password')" class="input-group-text"><i class="fa-regular fa-eye"></i></button>
                            </div>
                        </div>
                    </div>
                    <div class="custom-control custom-checkbox">
                        <input class="custom-control-input" type="checkbox" id="changePassword">
                        <label for="changePassword" class="custom-control-label">เปลี่ยนรหัสผ่าน</label>
                    </div>
                    <p class="err-validate text-danger" id="validate-password"></p>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group my-1">
                                <label>ชื่อเจ้าหน้าที่</label>
                                <input type="text" id="officer-fname" class="form-control" placeholder="ป้อนชื่อเจ้าหน้าที่">
                            </div>
                            <p class="err-validate text-danger" id="validate-fname"></p>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group my-1">
                                <label>นามสกุล</label>
                                <input type="text" id="officer-lname" class="form-control" placeholder="ป้อนชื่อเจ้าหน้าที่">
                            </div>
                            <p class="err-validate text-danger" id="validate-lname"></p>
                        </div>
                    </div>

                    <h6>ระดับการดูแล</h6>
                    <div class="custom-control custom-radio">
                        <input class="custom-control-input" value="librarian" type="radio" name="officer-role" id="librarian">
                        <label for="librarian" class="custom-control-label">บรรณารักษ์</label>
                    </div>
                    <div class="custom-control custom-radio">
                        <input class="custom-control-input" type="radio" value="admin" name="officer-role" id="admin">
                        <label for="admin" class="custom-control-label">ผู้ดูแลระบบ</label>
                    </div>
                    <p class="err-validate text-danger" id="validate-role"></p>
                </form>
            </div>
            <div class="modal-footer">
                <button class="btn bg-gradient-danger text-white" data-dismiss="modal">
                    ปิด
                </button>
                <button class="btn bg-gradient-secondary text-white" id="officerHandleSubmit">
                    เพิ่ม
                </button>
            </div>
        </div>
    </div>
</div>
