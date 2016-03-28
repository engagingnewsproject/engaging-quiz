<?
//http://code.tutsplus.com/tutorials/secure-your-forms-with-form-keys--net-4753
//You can of course choose any name for your class or integrate it in something like a functions or base class
class Enp_quiz_Nonce {
    //Here we store the generated form key
    private $nonce;

    //Here we store the old form key (more info at step 4)
    private $old_nonce;

    //The constructor stores the form key (if one excists) in our class variable
    function __construct()
    {
        //We need the previous key so we store it
        if(isset($_SESSION['enp_quiz_nonce']))
        {
            $this->old_nonce = $_SESSION['enp_quiz_nonce'];
        }
    }

    //Function to generate the form key
    private function generateKey()
    {
        //Get the IP-address of the user
        $ip = $_SERVER['REMOTE_ADDR'];

        //We use mt_rand() instead of rand() because it is better for generating random numbers.
        //We use 'true' to get a longer string.
        //See http://www.php.net/mt_rand for a precise description of the function and more examples.
        $uniqid = uniqid(mt_rand(), true);

        //Return the hash
        return md5($ip . $uniqid);
    }


    //Function to output the form key
    public function outputKey()
    {
        //Generate the key and store it inside the class
        $this->nonce = $this->generateKey();
        //Store the form key in the session
        $_SESSION['enp_quiz_nonce'] = $this->nonce;

        //Output the form key
        echo "<input type='hidden' name='enp_quiz_nonce' id='enp_quiz_nonce' value='".$this->nonce."' />";
    }


    //Function that validated the form key POST data
    public function validate()
    {
        //We use the old nonce and not the new generated version
        if($_POST['enp_quiz_nonce'] == $this->old_nonce)
        {
            //The key is valid, return true.
            return true;
        }
        else
        {
            //The key is invalid, return false.
            return false;
        }
    }
}

?>
