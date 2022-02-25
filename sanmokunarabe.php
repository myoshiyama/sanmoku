<?php 

$yourGet = [];
$enemyGet = [];
$boardValue = [ 1,2,3,4,5,6,7,8,9 ];

//ゲームのルール
class Conditions{

    //現在取得できる番号
    public function boardStatus(){
        global $boardValue;
        for($i=0;$i<3;$i++){
            echo $boardValue[$i];
        }
        echo "\n";
        for($i;$i<6;$i++){
            echo $boardValue[$i];
        }
        echo "\n";
        for($i;$i<9;$i++){
            echo $boardValue[$i];
        }
        echo "\n";
    }

    //先攻後攻決め
    public function whichFirst(){
        echo "先攻後攻どっち？ 先攻=> y , 後攻=> n\n";
        do{
            $answer = trim(fgets(STDIN));
            if( $answer == "y"){
                return true;
            }elseif( $answer == "n"){
                return false;
            }else{
                echo "yかnのどちらかを入力してください!\n";
            }
        }while($answer != "y" || $answer != "n");
    }

    //マスが取れるかどうか確認_プレイヤー用
    public function confirmYours($yourChoice){
        global $yourGet;
        global $boardValue;
        if( in_array($yourChoice,$boardValue) ){
            $boardValue = str_replace($yourChoice,"O",$boardValue);
            array_push($yourGet,$yourChoice);
            return 1;
        }else{
            echo "もうすでに取られている数字です!\n";
            return 0;
        }
    }

    //マスが取れるかどうか確認_敵用
    public function confirmEnemys($enemyChoice){
        global $enemyGet;
        global $boardValue;
        if( in_array($enemyChoice,$boardValue) ){
            $boardValue = str_replace($enemyChoice,'X',$boardValue);
            array_push($enemyGet,$enemyChoice);
            return 1;
        }else{
            return 0;
        }
    }

    //勝利条件
    public function victoryJudgment($player){
        global $yourGet;
        global $enemyGet;
        if($player=="you"){
            $getMark = $yourGet;
        }else{
            $getMark = $enemyGet;
        }
        $victoryArray = [
            //横
            [1,2,3],[4,5,6],[7,8,9],
            //縦
            [1,4,7],[2,5,8],[3,6,9],
            //斜め
            [1,5,9],[3,5,7]
        ];
        for($i=0;$i<8;$i++){
            $matchValue = array_intersect($getMark,$victoryArray[$i]);
            if(count($matchValue)==3){
                if($player=="you"){
                    echo "あなたの勝ちです!\n";
                }else{
                    echo "あなたの負けです!\n";
                }
                return 1;
                break;
            }
        }
    }

    //引き分け判定
    public function drawJudgment(){
        global $yourGet;
        global $enemyGet;
        if(count($yourGet) + count($enemyGet) ==9){
            return true;
        }else{
            return false;
        }
    }

}

class Action{
    //自分が取るマスの入力
    public function yourTurn(){
        echo "あなたのターンです。取りたい番号を入力してください。\n";
        do{
            $yourChoice = trim(fgets(STDIN));
            if( $yourChoice > 0 && 10 > $yourChoice ){
                return $yourChoice;
            }else{
                echo "1~9でまたボードに存在する番号を入力してください。\n";
            }
        }while($yourChoice < 1 || 9 < $yourChoice);
    }

    //敵が取るマスの決定
    public function enemyTurn(){
        $enemyChoice = mt_rand(1,9);
        return $enemyChoice;
    }
}

//////////////////////////////////////////////

$condition = new Conditions();
$action = new Action();

//先攻の時
if($condition->whichFirst()){
    do{
        $yourChoice = $action->yourTurn();
        $confirm = $condition->confirmYours($yourChoice);
    }while($confirm != 1);
    $confirm = NULL;
    if( count($yourGet)>=3 ){
        $judgmemt = $condition->victoryJudgment("you");
    }
    $condition->boardStatus();
}

do{    
    //敵のターン
    do{
        $enemyChoice = $action->enemyTurn();
        $confirm = $condition->confirmEnemys($enemyChoice);
    }while($confirm != 1);
    $confirm = NULL;
    if( count($enemyGet)>=3 ){
        $judgmemt = $condition->victoryJudgment("enemy");
        if($judgmemt==1){
            $condition->boardStatus();
            break;
        }
    }
    if($condition->drawJudgment()){
        echo "引き分けです。";
        break;
    }
    echo "敵が数字を取り以下の状況です。\n";
    $condition->boardStatus();
    //自分のターン
    do{
        $yourChoice = $action->yourTurn();
        $confirm = $condition->confirmYours($yourChoice);
    }while($confirm != 1);
    $confirm = NULL;
    if( count($yourGet)>=3 ){
        $judgmemt = $condition->victoryJudgment("you");
    }
    if($condition->drawJudgment() && $judgmemt!=1){
        echo "引き分けです。\n";
        break;
    }
    $condition->boardStatus();
}while($judgmemt != 1);
