<?php
namespace Org\Nx;
/**
 * author:zhongjin
 * time:2016/10/20 11:53
 * description: 二叉查找树
 */
//结点
class Node
{
    public $value;  
    public $left;  
    public $right;
}

//先序遍历 根节点 ---> 左子树 ---> 右子树  
function preorder($root){  
    $stack=array();  
    array_push($stack,$root);  
    while(!empty($stack)){  
        $center_node=array_pop($stack);  
        echo $center_node->value.' ';//先输出根节点  
        if($center_node->right!=null){  
            array_push($stack,$center_node->right);//压入左子树  
        }  
        if($center_node->left!=null){  
            array_push($stack,$center_node->left);  
        }  
    }  
}


//中序遍历，左子树---> 根节点 ---> 右子树  
function inorder($root){  
    $stack = array();  
    $center_node = $root;  
    while (!empty($stack) || $center_node != null) {  
             while ($center_node != null) {  
                 array_push($stack, $center_node);  
                 $center_node = $center_node->left;  
             }  
   
             $center_node = array_pop($stack);  
             echo $center_node->value . " ";  
   
             $center_node = $center_node->right;  
         }  
}