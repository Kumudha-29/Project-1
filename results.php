<?php
include('db_connect.php');
session_start();

// Check if user is admin
$isAdmin = isset($_SESSION['admin']);

// Fetch all elections
$elections = $conn->query("SELECT * FROM elections ORDER BY start_date ASC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Election Results</title>
<link rel="stylesheet" href="style.css">
<style>
.results-container { max-width: 700px; margin: 50px auto; background: #f9f9f9; border-radius: 12px; padding: 30px; box-shadow: 0 0 10px rgba(0,0,0,0.1); text-align: center; }
.alert { font-size: 1.1em; color: #555; background: #ffe5e5; padding: 15px; border-radius: 8px; margin-bottom: 20px; border: 1px solid #ffaaaa; }
table { width: 100%; border-collapse: collapse; margin-top: 15px; }
th, td { border: 1px solid #ddd; padding: 10px; }
th { background: #007BFF; color: white; }
h2 { margin-bottom: 15px; }
.back-link { display: inline-block; margin-top: 20px; background: #007BFF; color: white; padding: 8px 15px; border-radius: 6px; text-decoration: none; }
.back-link:hover { background: #0056b3; }
.winner-message { margin-top: 25px; font-size: 1.2em; font-weight: bold; color: #1e3a8a; background: #e7f3ff; padding: 15px; border-radius: 10px; border: 1px solid #bcdcff; }
.draw-message { margin-top: 25px; font-size: 1.2em; font-weight: bold; color: #8a1e1e; background: #ffecec; padding: 15px; border-radius: 10px; border: 1px solid #ffbcbc; }
.election-title { margin-top: 30px; font-size: 1.3em; font-weight: bold; color: #333; }
</style>
</head>

<body style="background-image: url('Voting.png'); 
             background-size: cover; 
             background-repeat: no-repeat; 
             background-position: center center; 
             min-height: 100vh;
             margin: 0;">


<header>
  <h1>Election Results</h1>
</header>

<div class="results-container">

<?php
if ($elections->num_rows > 0) {
    while ($election = $elections->fetch_assoc()) {
        $election_id = $election['id'];
        $election_name = $election['name'];

        echo "<div class='election-title'>{$election_name}</div>";

        // Only show results if admin OR results released for this election
        if ($isAdmin || $election['results_released'] == '1') {

            $query = $conn->query("
                SELECT c.name AS candidate_name, COUNT(v.id) AS vote_count
                FROM candidates c
                LEFT JOIN votes v ON c.id = v.candidate_id
                WHERE c.election_id = $election_id
                GROUP BY c.id
                ORDER BY vote_count DESC
            ");

            if ($query->num_rows > 0) {
                echo "<table>
                        <tr>
                            <th>Candidate Name</th>
                            <th>Total Votes</th>
                        </tr>";

                $candidates = [];
                while ($row = $query->fetch_assoc()) {
                    $candidates[] = $row;
                    echo "<tr>
                            <td>{$row['candidate_name']}</td>
                            <td>{$row['vote_count']}</td>
                          </tr>";
                }
                echo "</table>";

                // Determine winner or draw
                $highestVotes = $candidates[0]['vote_count'];
                $topCandidates = [];
                foreach ($candidates as $cand) {
                    if ($cand['vote_count'] == $highestVotes) {
                        $topCandidates[] = $cand['candidate_name'];
                    }
                }

                if (count($topCandidates) > 1) {
                    $drawNames = implode(', ', $topCandidates);
                    echo "<div class='draw-message'>⚖️ It's a draw between <strong>{$drawNames}</strong> with <strong>{$highestVotes}</strong> votes each!</div>";
                } else {
                    $winnerName = $topCandidates[0];
                    echo "<div class='winner-message'>🎉 Congratulations to <strong>{$winnerName}</strong> for winning the election with <strong>{$highestVotes}</strong> votes!</div>";
                }

            } else {
                echo "<p>No votes have been cast for this election yet.</p>";
            }

        } else {
            echo "<div class='alert'>⚠️ Results for <strong>{$election_name}</strong> have not been released yet. Please check back later.</div>";
        }
    }

    if ($isAdmin) {
        echo "<a href='admin_dashboard.php' class='back-link'>⬅ Back to Dashboard</a>";
    }

} else {
    echo "<p>No elections found.</p>";
}
?>
</div>

<footer>
  <p>© 2025 E-Voting System | Secure and Transparent Elections</p>
</footer>

</body>
</html>
