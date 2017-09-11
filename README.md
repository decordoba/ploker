# Ploker
Voting system with a moderator and guests. Automatized planning poker.

## Explanation

This application is really useful in an Agile environment, where a team has to commit to finishing some of the stories assigned to them
every Sprint, but to know what they can get done in time, they first have to size every story (decide how long it will take to complete each of them).

One of the most used methods for sizing stories is Planning Poker, in which every member of the team votes, using cards, at the same time,
which avoids biases based on what others have voted.

The problem with this system is the mechanics of voting at the same time, without being biased by the rest of the team. To solve these
issues we have developed Ploker, a system that allows everyone to vote separately, and the votes will only be revealed when the
moderator decides so. This allows every user to select their choice, submit it, and the results will only be shown once everyone has
secured their vote. This also allows long distance voting, which was not possible with the original planning poker, because it was
necessary to see everyone to coordinate to vote at the same time.

With our system, we also log what every user is voting, as well as other statistics, so it is possible to apply machine learning with the 
data gathered, and find trends in how the sizing is done by different teams.

## Interface

The following screenshots depict the use of the interface:

1. In the ploker url, we will find a dialog that allows the user to create a new room and become the moderator, or to join as a guest an
already existing room.

![Join or manage a room](/README_images/Picture1.png?raw=true)

2. If the option manage a room is selected, the user will create a room with an assigned room number that will be used by guests to enter
the room. The screen shows a button to copy into the clipboard a link for the guests to easily access the room, and a list of all the
guests that have already entered the room.

![Creating a room](/README_images/Picture2.png?raw=true)

3. If the user selected join a room in the first interface window, he will be promted with a dialog to write their username, and the
room number they are in. Once they have selected these fields, they will be able to join the room. 

![Creating a room](/README_images/Picture3.png?raw=true)

4. While the poll does not start, the guests will be prompted with the following window, indicating them to wait.

![Creating a room](/README_images/Picture4.png?raw=true)

5. Observe that the moderator can monitor who has entered the room at all times, and start the poll whenever everyone has joined.
Also, if a user has been inactive for more than 30 seconds (for example, if he closes the browser window), he will disappear from the list.

![Creating a room](/README_images/Picture5.png?raw=true)

![Creating a room](/README_images/Picture6.png?raw=true)

6. Once the poll has been started by the moderator, all users will be able to vote the size they consider most appropiate. We are using
a custom Fibonacci sequence to vote, with the cards 1, 2, 3, 5, 8, 13, 21, infinite, and coffee. Each user can submit a vote, and then
change their mind and vote again as many times as they want.

![Creating a room](/README_images/Picture7.png?raw=true)

7. The moderator can see who has and has not voted yet. This way he/she can wait until everyone has voted to reveal the results of the poll.

![Creating a room](/README_images/Picture8.png?raw=true)

![Creating a room](/README_images/Picture9.png?raw=true)

8. Once everyone has voted, the results of the number of votes for every option voted will be shown in both the guest and moderator screens,
so everyone can examine the results. If a result is hovered with the mouse, the people that voted that option will be shown.

![Creating a room](/README_images/Picture10.png?raw=true)

![Creating a room](/README_images/Picture11.png?raw=true)

9. Once the moderator decides it is time to start the next poll, he/she can click the Start Next Poll button and all the guests screens
will stop showing the previous results, and allow them to vote using the Fibonacci sequence in a completely new poll. Therefore, we go
back to step 6, and we can repeat this porcess for as many stories as it is necessary.

## Authors

This project was finished in 48 hours for a Hackathon at Express Scripts. The team consisted of 4 people:
 - Daniel de Cordoba Gil - Backend and (some) frontend
 - Tyler Corbett - Frontend
 - Arden Hawley - Frontend
 - Tyler Votaw - Backend
