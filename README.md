# Clash of Clans Member Database
Project Duration: 16 Feb 2021 - 2 Mar 2021
### Project Description
A Clan in the game _Clash of Clans_ refers to a group of players that join together to compete with other clans in two ways: the first is to pool trophies together, and the second is to win Clan Wars for loot and Clan XP. There are many of such _Clans_ in the game Clash of Clans. 

### Project Motivation
I am one of the leaders of my current Clan. One of the issues we were facing was to be able to regulate member contributions to the clan, but due to the game's interface, we are only allowed to see contributions from the members within a single month. We needed a method of persisting member contribution data in order to measure consistency and the extent of the member contributions compared to other players within the clan. On this basis, we would be able to not only recognise those who are more outspoken, but also those who have been quietly contributing in the background. 

As such, the first draft of this project was a <b>Google Sheet</b>, where we measured member contributions in these three aspects:
- Member Troop Donations
- Clan War Participation Count
- Clan Games Score

We tracked each of these contributions on separate spreadsheets. 

<b>The issue we faced </b> was that when more people joined our clan, we had to update every single spreadsheet, and when people left the clan, we also had to delete their data from every single spreadsheet. Not to mention that in every sheet there was no particular ordering among the members. It was tedious, and by two months, our Excel sheet was a lost cause. 

### Clash of Clans Member Database 
At that time, I coincidentally finished my Coursera Specialisation: _Web Applications For Everybody_ and it taught me how to create <b>database web applications in PHP</b>. I decided that the current issues we were facing in the clan was indeed an avenue to apply the knowledge that I had just gained. I noticed that a web application backed by a database could resolve the issues we were facing in the first iteration of our project.

#### Database Relational Model
The following is the essential details of our database model. In this Member Database, we would 
1. Create a table for the <code>Leaders</code> of this database application: which will be the only users for this application. Each of these leaders would have a username and password that would allow them to insert into and delete entries from the database. 
2. Create a table for <code>Members</code> which would store the following information about each member:
    - In-Game Name
    - Telegram Handle
    - Town Hall Level
    - A Bio (short description about them)
    - Attack Strategies that they use frequently
    - <b>A unique Primary Key</b> for easy insertion and deletion of data associated with this Member. 
4. We would then create a table for each of the metrics that we want to track:
    - Member Troop Donations in the table called <code>Donations</code>
    - Clan War Participation Count in the table called <code>ClanWars</code>
    - Clan Games Score in the table called <code>ClanGames</code>
    - each of these entries would have a <b> foreign key from the <code>Members</code> table</b>. As a result, we would be able to add an entry attached to a specific Member in any particular order. 
   -  In addition, we would be able to use this database to store data across months by recording member contributions by the month.

5. Lastly, we would have a page (called <code>about.php</code>) which gathers all the data from all the different tables related to a specific member, and display them on a single page. 



#### Limitations of our Database Model
New limitations surfaced after we resolved some basic issues.
1. <b>Data Collection process was too tedious</b>. Our data collection process was entirely separated from the game. Even though we can input entries related to a specific member in no particular order, we would still have to record and gather a large amount of data every month about each member. The most sustainable goal for all of us is to automate this data collection process.

2. <b> Members that leave the clan don't usually leave for good.</b> Sometimes members who need a break from the game will be removed from the clan. For the pilot of this application, we practiced deleting member data the moment where we remove them from the clan. However, this isn't representative of the situation in the game, because we would also invite members back when they become active again, and as a result of their inactivity their data has been deleted. This isn't a big issue - but the ideal fix for this would be to <b>create a member archive</b> that retains the data related to this particular member for a prolonged period of time. 

3. Last but not least, a free hosting site for this web application. Most of us leaders won't want to cash in the game, let alone cash in a website that is already on top of our commitment to a game. A few months after our pilot the free hosting service where we built the application on (Byethost) shut down the website and all the member information has been lost together with the hosting account. Apparently the website that we created wasn't receiving enough activity for them to sustain it, after all, we only allowed the leaders of the clan (just 5 of us) to use the website.

We are exploring other alternatives now, but this <b> was </b> the second attempt at regulating member contributions to the clan.
 
