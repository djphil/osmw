// OSMW_Npc_Terminal_v0.1.lsl by djphil (CC-BY-NC-SA 4.0)

float tempo = 5.0; 
key requestid; 
string ordre;
key npc;
 
appel_web(string parameter, string datas)
{
    string url =llGetObjectDesc();
    requestid = llHTTPRequest(url, [
        HTTP_METHOD, "POST", 
        HTTP_MIMETYPE, "application/x-www-form-urlencoded"
    ], "parameter=" + parameter + "&uuid=" + (string)llGetKey() + "&datas=" + datas + "&region=" + llGetRegionName());         
}

rez_objet(string parameter)
{
    rotation rot = llEuler2Rot(< 0, 90, 90> * DEG_TO_RAD);
    vector vec = llGetPos() + < 0.0, 0.0, 10.0>;
    vector speed = llGetVel();
    llRezAtRoot(parameter, vec, speed, rot, 10);
}

npc_load_appearance(string params)
{
    list buffer = llParseString2List(params, [" "], []);
    string msg0 = llList2String(buffer, 0);
    string msg1 = llList2String(buffer, 1);            
    string msg2 = llList2String(buffer, 2);
    string msg3 = llList2String(buffer, 3);

    if (msg0 == "load" && msg1 != "" && msg2 != "")
    {
        osNpcLoadAppearance(msg1, msg2 + " " + msg3);
        llOwnerSay("Loaded appearance " + msg2 + " to " + npc);
    }
}

npc_create(string params)
{
    list buffer = llParseString2List(params, [" "], []);
    string msg0 = llList2String(buffer, 0);
    string msg1 = llList2String(buffer, 1);            
    string msg2 = llList2String(buffer, 2);
    string msg3 = llList2String(buffer, 3);

    if (msg0 == "create")
    {
        if (msg1 != "" )
        {
            string firstname = "Default";
            string lastname = "Female";
            // llOwnerSay(llList2CSV(buffer));
            // llOwnerSay(msg3);

            string notecardName = msg1 + " " + msg2;
            
            if (msg2 != "" && msg3 != "")
            {                        
                list listname =llParseString2List(msg2, [";"], []); 
                firstname = llList2String(listname, 0);
                lastname = llList2String(listname, 1);   
                list listcoord = llParseString2List(msg3, [";"], []); 
                vector pos = <llList2Float(listcoord, 0), llList2Float(listcoord, 1), llList2Float(listcoord, 2)>;
                npc = osNpcCreate(firstname, lastname, llGetPos() + pos, notecardName);
                llOwnerSay("Created npc from notecard " + notecardName);                      
            }
            else
            {
                npc = osNpcCreate(firstname,lastname, llGetPos() + <5, 5, 5>, notecardName);
                llOwnerSay("Created npc from notecard " + notecardName);
            }
            
            llOwnerSay("Created npc UUID: " + npc);
            string url =llGetObjectDesc();
            requestid = llHTTPRequest(url, [
                HTTP_METHOD, "POST", 
                HTTP_MIMETYPE, "application/x-www-form-urlencoded"
            ], "parameter=NPC_CREATE&uuid="+npc+"&firstname="+firstname+"&lastname="+lastname+"&region="+llGetRegionName());
        }
        else {llOwnerSay("Usage: create <notecard-name>");}
    }
}

stop_all_NPC()
{
    llOwnerSay("Removing all NPCs from this scene!");

    // list avies = osGetNpcList();
    list avies = osGetAvatarList();
    integer n;

    for(n = 0; n < llGetListLength(avies); n += 3)
    {
        llOwnerSay("Attempting to remove " + llList2String(avies, n + 2) + " with UUID "+llList2String(avies, n + 0));
        osNpcRemove(llList2Key(avies, n));
        // llOwnerSay(llList2String(avies, n));
    }
}

default
{
    state_entry()
    {
        llOwnerSay("Enregistrement du Gestionnaire de NPC Web ...");
        appel_web("REG_WEB_NPC", "");        
        llSetTimerEvent(tempo);
    }

    touch_start(integer number)
    {
        appel_web("LISTE_NPC", "");
    }

    http_response(key request_id, integer status, list metadata, string body)
    {
        // llOwnerSay("DEBUG " + body);
        if (request_id == requestid)
        {
            body = llStringTrim(body, STRING_TRIM);
            list buffer = llCSV2List(body);
            string section0 = llStringTrim(llList2String(buffer, 0), STRING_TRIM);
            string section1 = llStringTrim(llList2String(buffer, 1), STRING_TRIM);
            string section2 = llStringTrim(llList2String(buffer, 2), STRING_TRIM);
            string section3 = llStringTrim(llList2String(buffer, 3), STRING_TRIM);
            string section4 = llStringTrim(llList2String(buffer, 4), STRING_TRIM);
            string section5 = llStringTrim(llList2String(buffer, 5), STRING_TRIM);

            if (section0 == "Info_NPC")
            {
                llOwnerSay(section1 + ": " + section2);
                list listRetour = llParseString2List(section3, [";"], []);
                integer n = llGetListLength(listRetour);
                integer i;

                for (i = 0; i < n; ++i)
                {
                    llOwnerSay((i + 1) + ") " + llList2String(listRetour, i));
                }
            }

            else if (section5 == llGetKey())
            {
                if (section0 == "Gestion_NPC")
                {
                    if (section1 == "STOP_ALL") {stop_all_NPC();}
                    else if (section1 == "REZ1") {rez_objet("Particule1");}
                    else if (section1 == "REZ2") {rez_objet("Particule2");}
                    else if (section1 == "REZ3") {rez_objet("Particule3");}

                    else if (section1 == "CREATE")
                    {
                        npc_create("create " + section2 + " " + section3 + " " + section4);
                    }

                    else if (section1 == "REMOVE_NPC") {osNpcSay(section2, "Goodbye !!!"); osNpcRemove(section2);}
                    else if (section1 == "SAY") {osNpcSay(section2, section3);}
                    else if (section1 == "ANIMATE_START") {osAvatarPlayAnimation(section2, section3);}
                    else if (section1 == "ANIMATE_STOP") {osAvatarStopAnimation(section2, section3);}
                    else if (section1 == "SIT") {osNpcSit(section2, section3, OS_NPC_SIT_NOW);}
                    else if (section1 == "STAND") {osNpcStand(section2);}
                    else if (section1 == "APPARENCE_LOAD") {npc_load_appearance("load "+section2+" "+section3 );}
                    else if (section1 == "APPARENCE_SAVE") {osNpcSaveAppearance(section2, section3); llOwnerSay("Appearance seved " + section2 + " to " + npc);}

                    else if (section1 == "CLONE_OWNER") // if (msg0 == "clone")
                    {
                        if (section2 != "")
                        {
                            osOwnerSaveAppearance(section2);
                            llOwnerSay("Cloned your appearance to " + section2);
                        }
                        else {llOwnerSay("Usage: clone <notecard-name-to-save>");}
                    }
                    
                    else if (section1 == "MOVE")
                    {
                        if (section2 != "" && section3 != "" && npc != NULL_KEY)
                        {                
                            vector delta = <(integer)section2, (integer)section3, 0.0>;

                            if (section4 != "")
                            {
                                delta.z = (integer)section4;
                            }
                            osNpcMoveTo(npc, osNpcGetPos(npc) + delta);
                            llOwnerSay("Move your NPC to " + (string)delta);
                        }                          
                        else {llOwnerSay("Usage: move <x> <y> [<z>]");}
                    }
                    
                    else if (section1 == "MOVETO")
                    {
                        if (section2 != "" && section3 != "" && npc != NULL_KEY)
                        {                
                            vector pos = <(integer)section2, (integer)section3, 0.0>;

                            if (section4 != "")
                            {
                                pos.z = (integer)section4;
                            }

                            osNpcMoveTo(npc, pos);
                            llOwnerSay("Move your NPC to " + (string)pos);
                        }                          
                        else {llOwnerSay("Usage: move <x> <y> [<z>]");}
                    }

                    else if (section1 == "MOVETOTARGET")
                    {
                        osNpcMoveToTarget(npc, llGetPos() + <9,9,5>, OS_NPC_FLY|OS_NPC_LAND_AT_TARGET);
                    }

                    else if (section1 == "MOVETOTARGETNOLAND")
                    {
                        osNpcMoveToTarget(npc, llGetPos() + <9,9,5>, OS_NPC_FLY);
                    }

                    else if (section1 == "MOVETOTARGETWALK")
                    {
                        osNpcMoveToTarget(npc, llGetPos() + <9,9,0>, OS_NPC_NO_FLY);
                    }

                    else if (section1 == "ROT")
                    {
                        vector xyz_angles = <0.0, 0.0, 90.0>; // This is to define a 1 degree change
                        vector angles_in_radians = xyz_angles * DEG_TO_RAD; // Change to Radians
                        rotation rot_xyzq = llEuler2Rot(angles_in_radians); // Change to a Rotation                
                        rotation rot = osNpcGetRot(npc);
                        osNpcSetRot(npc, rot * rot_xyzq);
                    }

                    else if (section1 == "ROT")
                    {
                        vector xyz_angles = <0.0, 0.0, (integer)section2>;
                        vector angles_in_radians = xyz_angles * DEG_TO_RAD; // Change to Radians
                        rotation rot_xyzq = llEuler2Rot(angles_in_radians); // Change to a Rotation                
                        osNpcSetRot(npc, rot_xyzq);
                    }

                    else if (section1 == "STOP")
                    {
                        osNpcStopMoveToTarget(npc);
                    }

                    else if (section1 == "LISTING")       
                    {
                        integer nb_notecard = llGetInventoryNumber(INVENTORY_NOTECARD);
                        string inventaire = "apparence;" + nb_notecard + ";";
                        integer x;  
        
                        for (x = 0; x < nb_notecard; x++)
                        {
                            inventaire = inventaire + llGetInventoryName(INVENTORY_NOTECARD, x) + ";";
                        }

                        integer nb_anaimation = llGetInventoryNumber(INVENTORY_ANIMATION);
                        inventaire = inventaire + "animation;" + nb_anaimation + ";";
        
                        for (x = 0; x < nb_anaimation; x++)
                        {
                            inventaire = inventaire + llGetInventoryName(INVENTORY_ANIMATION, x) + ";";
                        }

                        appel_web("LISTE_OBJ", inventaire);
                    }

                    else {llOwnerSay("I don't understand [" + section1 + "]");}
                }

                else {llOwnerSay("I don't understand [" + section0 + "]");}
            }
        }
    }

    timer() {appel_web("TIMER", "");}

    changed(integer change)  
    {
        if (change == CHANGED_INVENTORY)
        {
            llOwnerSay("Changement Inventaire, RECHARGER DEPUIS L'INTERFACE WEB");
        }
    }

    on_rez(integer param) {llResetScript();}
}