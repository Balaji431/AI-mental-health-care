package com.example.mindcare.ui.screens

import androidx.compose.foundation.layout.Column
import androidx.compose.foundation.layout.Spacer
import androidx.compose.foundation.layout.fillMaxSize
import androidx.compose.foundation.layout.fillMaxWidth
import androidx.compose.foundation.layout.height
import androidx.compose.foundation.layout.padding
import androidx.compose.foundation.lazy.LazyColumn
import androidx.compose.foundation.lazy.items
import androidx.compose.material3.ExperimentalMaterial3Api
import androidx.compose.material3.Scaffold
import androidx.compose.material3.SnackbarHost
import androidx.compose.material3.SnackbarHostState
import androidx.compose.material3.Text
import androidx.compose.material3.rememberScaffoldState
import androidx.compose.runtime.Composable
import androidx.compose.runtime.LaunchedEffect
import androidx.compose.runtime.getValue
import androidx.compose.runtime.mutableStateOf
import androidx.compose.runtime.remember
import androidx.compose.runtime.rememberCoroutineScope
import androidx.compose.runtime.setValue
import androidx.compose.ui.Modifier
import androidx.compose.ui.unit.dp
import kotlinx.coroutines.launch
import com.example.mindcare.data.FakeData
import com.example.mindcare.model.Mood
import com.example.mindcare.ui.components.GradientHeader
import com.example.mindcare.ui.components.InsightCard
import com.example.mindcare.ui.components.MoodSelector
import com.example.mindcare.ui.components.ProgressCard
import com.example.mindcare.ui.components.QuickActionCard

@OptIn(ExperimentalMaterial3Api::class)
@Composable
fun HomeScreen(
    innerPadding: androidx.compose.foundation.layout.PaddingValues,
    onOpenCheckIn: () -> Unit,
    onOpenBreathing: () -> Unit,
    onOpenMeditation: () -> Unit,
    onOpenMoodCalendar: () -> Unit
) {
    var selectedMood by remember { mutableStateOf<Mood?>(Mood.GOOD) }
    var showCheckInSheet by remember { mutableStateOf(false) }
    val snackbarHostState = remember { SnackbarHostState() }
    val scope = rememberCoroutineScope()

    if (showCheckInSheet) {
        CheckInBottomSheet(
            onDismiss = { showCheckInSheet = false },
            onComplete = { mood, emotions, _ ->
                selectedMood = mood
                scope.launch {
                    snackbarHostState.showSnackbar(
                        message = "Check-in saved (${mood?.label ?: "Unknown"})"
                    )
                }
            }
        )
    }

    Scaffold(
        modifier = Modifier
            .fillMaxSize()
            .padding(innerPadding),
        snackbarHost = { SnackbarHost(hostState = snackbarHostState) }
    ) { contentPadding ->
        LazyColumn(
            modifier = Modifier
                .fillMaxSize()
                .padding(contentPadding)
        ) {
            item {
                GradientHeader(
                    title = "Hi, how's your mind today?",
                    subtitle = "Tap a mood to check in and get personalized insights.",
                    emoji = selectedMood?.emoji ?: "💜"
                )
            }
            item {
                Spacer(modifier = Modifier.height(16.dp))
                MoodSelector(
                    selected = selectedMood,
                    onMoodSelected = { selectedMood = it }
                )
            }
            item {
                Spacer(modifier = Modifier.height(16.dp))
                Text(
                    text = "Today's emotional snapshot",
                    modifier = Modifier.padding(horizontal = 20.dp)
                )
                ProgressCard(
                    title = "Balance & resilience",
                    description = "You're building a steady check-in habit. Keep going!",
                    progress = 0.6f
                )
            }
            item {
                Spacer(modifier = Modifier.height(16.dp))
                Text(
                    text = "Quick actions",
                    modifier = Modifier.padding(horizontal = 20.dp)
                )
                Column(
                    modifier = Modifier
                        .fillMaxWidth()
                        .padding(horizontal = 16.dp, vertical = 8.dp)
                ) {
                    QuickActionCard(
                        title = "Guided check-in",
                        subtitle = "Reflect on your mood in under a minute.",
                        emoji = "📝",
                        onClick = { showCheckInSheet = true }
                    )
                    Spacer(modifier = Modifier.height(8.dp))
                    QuickActionCard(
                        title = "Calm breathing",
                        subtitle = "Slow down with a 4-7-8 exercise.",
                        emoji = "🌬️",
                        onClick = onOpenBreathing
                    )
                    Spacer(modifier = Modifier.height(8.dp))
                    QuickActionCard(
                        title = "Mini meditation",
                        subtitle = "Take 3 mindful minutes.",
                        emoji = "🧘",
                        onClick = onOpenMeditation
                    )
                }
            }
            item {
                Spacer(modifier = Modifier.height(16.dp))
                Text(
                    text = "AI mood insights",
                    modifier = Modifier.padding(horizontal = 20.dp)
                )
            }
            items(FakeData.insights) { insight ->
                InsightCard(insight = insight)
            }
            item { Spacer(modifier = Modifier.height(24.dp)) }
        }
    }
}


