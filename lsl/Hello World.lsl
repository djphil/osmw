default
{
    state_entry()
    {
        llSay(PUBLIC_CHANNEL, "Hello, World!");
    }
 
    touch_start(integer total_number)
    {
        llSay(PUBLIC_CHANNEL, "Touched.");
    }
}
