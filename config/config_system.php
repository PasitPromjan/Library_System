<?php
class CompareUsername
{
    private  $username = 'admin';
    private $password = '123456';
    private $role = 'admin';
    private  $id = 'admin';
    private  $name = 'ผู้ดูแลระบบ';

    function compare($username, $password)
    {
        if ($username == $this->username && $password == $this->password) {
            return [
                'username'=>$this->username,
                'role'=>$this->role,
                'id'=>$this->id,
                'name'=>$this->name
            ];
        } else {
            return false;
        }
    }
}
