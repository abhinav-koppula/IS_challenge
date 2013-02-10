<?php
include(APPPATH.'/libraries/REST_Controller.php');
class Handle extends REST_Controller
{
    public function index_post() 
    {
        $this->load->database();
        
        $name=$this->post('name');
        $author=$this->post('author');
        $status=$this->post('status');
        
        $insert = "insert into books(name,author,status) values('$name','$author',$status)";
        
        $q=$this->db->query($insert);
        echo $this->db->insert_id();
        
    }
    
    public function index_put()
    {
            $this->load->database();
            
            $name=$this->put('name');
            $author=$this->put('author');
            $status=$this->put('status');
            $id=$this->put('id');
            
            $update="update books set name='$name',author='$author',status=$status where id=$id";
            $this->db->query($update);
     
    }
    
    public function index_delete()
    {
        $id = $this->delete('id');
        
        $this->load->database();
        if($id!='')
        {
        $select = "select * from books where id=$id";
        $query= $this->db->query($select);
        
        $delete="DELETE FROM `books` WHERE id=$id";
        
        $this->db->query($delete);
        }
        
    }
    
    
}
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
?>
